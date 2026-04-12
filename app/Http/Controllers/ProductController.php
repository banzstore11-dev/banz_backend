<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPriceTier;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\GetProductPriceRequest;
use App\Mail\AdminProductActivity;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Product::with(['category', 'priceTiers', 'images']);

        // Filter by category
        if (isset($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        // Filter by active status
        if (isset($validated['is_active'])) {
            $query->where('is_active', $validated['is_active']);
        }

        // Filter by featured
        if (isset($validated['is_featured'])) {
            $query->where('is_featured', $validated['is_featured']);
        }

        // Search by name or description
        if (isset($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $perPage = $validated['per_page'] ?? 15;
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Parse JSON strings from FormData if present
        $priceTiers = $this->parseJsonField($validated['price_tiers'] ?? null);
        $images = $this->parseJsonField($validated['images'] ?? null);
        $imageMetadata = $this->parseJsonField($validated['image_metadata'] ?? null);
        
        unset($validated['price_tiers'], $validated['images'], $validated['image_metadata']);

        $product = Product::create($validated);

        // Create price tiers if provided
        if ($priceTiers) {
            foreach ($priceTiers as $tier) {
                ProductPriceTier::create([
                    'product_id' => $product->id,
                    'min_quantity' => $tier['min_quantity'],
                    'max_quantity' => $tier['max_quantity'] ?? null,
                    'price' => $tier['price'],
                ]);
            }
        }

        // Handle image files - upload and generate URLs
        if ($request->hasFile('image_files')) {
            $imageFiles = $request->file('image_files');
            
            // Ensure imageFiles is an array
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }
            
            foreach ($imageFiles as $index => $file) {
                // Upload file to storage and generate URL
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $relativePath = 'storage/' . $path;
                
                // Get image metadata from request if provided
                $imageMeta = $imageMetadata[$index] ?? [];
                
                // Save image record with generated URL
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $relativePath,
                    'sort_order' => $imageMeta['sort_order'] ?? $index,
                    'is_primary' => $imageMeta['is_primary'] ?? ($index === 0),
                ]);
            }
        }

        $product->load(['category', 'priceTiers', 'images']);

        // Send admin notification for new product
        NotificationService::notifyAdmin(new AdminProductActivity($product, 'created'));

        // Check for low stock
        NotificationService::checkLowStock($product);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $identifier): JsonResponse
    {
        $product = Product::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        $product->load(['category', 'priceTiers', 'images']);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $identifier): JsonResponse
    {
        $product = Product::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        $validated = $request->validated();
        
        // Parse JSON strings from FormData if present
        $priceTiers = $this->parseJsonField($validated['price_tiers'] ?? null);
        $imageMetadata = $this->parseJsonField($validated['image_metadata'] ?? null);
        
        unset($validated['price_tiers'], $validated['image_metadata']);

        $product->update($validated);

        // Update price tiers if provided
        if ($priceTiers !== null) {
            // Delete existing price tiers
            $product->priceTiers()->delete();

            // Create new price tiers
            foreach ($priceTiers as $tier) {
                ProductPriceTier::create([
                    'product_id' => $product->id,
                    'min_quantity' => $tier['min_quantity'],
                    'max_quantity' => $tier['max_quantity'] ?? null,
                    'price' => $tier['price'],
                ]);
            }
        }

        // Update images only if new files are provided
        // If no new files are sent, existing images are kept
        if ($request->hasFile('image_files')) {
            // Delete existing images when new ones are uploaded
            $product->images()->delete();

            // Handle image files - upload and generate URLs
            $imageFiles = $request->file('image_files');
            
            // Ensure imageFiles is an array
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }
            
            foreach ($imageFiles as $index => $file) {
                // Upload file to storage and generate URL
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $relativePath = 'storage/' . $path;
                
                // Get image metadata from request if provided
                $imageMeta = $imageMetadata[$index] ?? [];
                
                // Save image record with generated URL
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $relativePath,
                    'sort_order' => $imageMeta['sort_order'] ?? $index,
                    'is_primary' => $imageMeta['is_primary'] ?? ($index === 0),
                ]);
            }
        }
        // If no new files are provided, existing images remain unchanged

        $product->load(['category', 'priceTiers', 'images']);

        // Send admin notification for product update
        NotificationService::notifyAdmin(new AdminProductActivity($product, 'updated'));

        // Check for low stock after update
        NotificationService::checkLowStock($product);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh(['category', 'priceTiers', 'images']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $identifier): JsonResponse
    {
        $product = Product::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        // Send admin notification before deletion
        NotificationService::notifyAdmin(new AdminProductActivity($product, 'deleted'));

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Parse JSON field from FormData (handles both JSON strings and arrays)
     */
    private function parseJsonField($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded !== null ? $decoded : $value;
        }
        return $value;
    }

    /**
     * Get price for a specific quantity.
     */
    public function getPrice(GetProductPriceRequest $request, string $identifier): JsonResponse
    {
        $product = Product::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        $validated = $request->validated();
        $quantity = $validated['quantity'];
        $price = $product->getPriceForQuantity($quantity);

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $price * $quantity,
            ],
        ]);
    }
}
