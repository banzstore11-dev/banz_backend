<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Get admin email address from config or environment
     */
    public static function getAdminEmail(): string
    {
        return config('mail.admin_email', env('ADMIN_EMAIL', config('mail.from.address')));
    }

    /**
     * Get all admin users for database notifications
     */
    public static function getAdmins()
    {
        return \App\Models\User::where('email', 'admin@banz.com')
            ->orWhere('name', 'like', '%Admin%')
            ->get();
    }

    /**
     * Send notification to admin (Mail + Database)
     */
    public static function notifyAdmin($notification): void
    {
        try {
            $admins = self::getAdmins();
            
            if ($admins->isEmpty()) {
                Log::warning('No admin users found for database notifications. Falling back to configured admin email for mail.');
                $adminEmail = self::getAdminEmail();
                if ($adminEmail) {
                    \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)->notify($notification);
                }
                return;
            }

            \Illuminate\Support\Facades\Notification::send($admins, $notification);
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a specific user
     */
    public static function notifyUser($user, $notification): void
    {
        try {
            if (is_string($user)) {
                \Illuminate\Support\Facades\Notification::route('mail', $user)->notify($notification);
            } else {
                $user->notify($notification);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send user notification: ' . $e->getMessage());
        }
    }

    /**
     * Check if product stock is low and send alert
     */
    public static function checkLowStock($product, $threshold = 10): void
    {
        if ($product->stock_quantity <= $threshold) {
             // We can create a dedicated StockNotification later if needed
             // For now, keep it simple or use a generic notification
        }
    }
}
