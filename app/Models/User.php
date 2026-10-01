<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Roles Constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SHOP_MANAGER = 'shop_manager';
    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_DEMO_ADMIN = 'demo_admin';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'permissions',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    /**
     * Relationship with Customer Orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Role Helper Methods.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isShopManager(): bool
    {
        return $this->role === self::ROLE_SHOP_MANAGER;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function isDemoAdmin(): bool
    {
        return $this->role === self::ROLE_DEMO_ADMIN;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if user is eligible to access the administration console.
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SHOP_MANAGER, self::ROLE_EMPLOYEE, self::ROLE_DEMO_ADMIN]);
    }

    /**
     * Human-readable Role Label in Bengali.
     */
    public function getRoleTitleAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'সুপার অ্যাডমিন (Administrator)',
            self::ROLE_SHOP_MANAGER => 'শপ ম্যানেজার (Shop Manager)',
            self::ROLE_EMPLOYEE => 'এমপ্লয়ি / স্টাফ (Employee)',
            self::ROLE_DEMO_ADMIN => 'ডেমো অ্যাডমিন (Read-Only Demo)',
            default => 'গ্রাহক (Customer)',
        };
    }

    /**
     * Badge CSS class for roles.
     */
    public function getRoleBadgeStyleAttribute(): array
    {
        return match ($this->role) {
            self::ROLE_ADMIN => [
                'bg' => 'rgba(16, 185, 129, 0.15)',
                'color' => '#047857',
                'border' => '#a7f3d0',
                'icon' => 'fa-shield-alt',
                'label' => 'অ্যাডমিনিস্ট্রেটর',
            ],
            self::ROLE_SHOP_MANAGER => [
                'bg' => 'rgba(99, 102, 241, 0.14)',
                'color' => '#4338ca',
                'border' => '#c7d2fe',
                'icon' => 'fa-user-tie',
                'label' => 'শপ ম্যানেজার',
            ],
            self::ROLE_EMPLOYEE => [
                'bg' => 'rgba(245, 158, 11, 0.15)',
                'color' => '#b45309',
                'border' => '#fde68a',
                'icon' => 'fa-id-badge',
                'label' => 'এমপ্লয়ি / স্টাফ',
            ],
            self::ROLE_DEMO_ADMIN => [
                'bg' => 'rgba(245, 158, 11, 0.15)',
                'color' => '#b45309',
                'border' => '#fde68a',
                'icon' => 'fa-eye',
                'label' => 'ডেমো অ্যাডমিন (Read-Only)',
            ],
            default => [
                'bg' => 'rgba(59, 130, 246, 0.12)',
                'color' => '#1d4ed8',
                'border' => '#bfdbfe',
                'icon' => 'fa-user',
                'label' => 'গ্রাহক (Customer)',
            ],
        };
    }

    /**
     * Check if the user has a specific permission.
     */
    public function canDo(string $permission): bool
    {
        // Super Admin and Demo Admin can view console pages
        if ($this->isAdmin() || $this->isDemoAdmin()) {
            return true;
        }

        // If custom permissions are explicitly configured for this user
        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions);
        }

        // Otherwise fallback to role default preset
        $roleDefaults = self::getDefaultRolePermissions($this->role);
        return in_array($permission, $roleDefaults);
    }

    /**
     * Alias for canDo.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->canDo($permission);
    }

    /**
     * Role default permissions.
     */
    public static function getDefaultRolePermissions(string $role): array
    {
        return match ($role) {
            self::ROLE_ADMIN => array_keys(self::getAllPermissionsList()),
            self::ROLE_SHOP_MANAGER => [
                'orders.view',
                'orders.status',
                'products.view',
                'products.create',
                'products.edit',
                'products.delete',
                'categories.manage',
                'finance.view',
                'banners.manage',
                'coupons.manage',
                'users.view',
                'integrations.manage',
            ],
            self::ROLE_EMPLOYEE => [
                'orders.view',
                'orders.status',
                'products.view',
            ],
            default => [],
        };
    }

    /**
     * All system permissions organized by category for UI assignment.
     */
    public static function getAllPermissionsGrouped(): array
    {
        return [
            'অর্ডার ব্যবস্থাপনা (Orders Management)' => [
                'orders.view'   => 'অর্ডার তালিকা ও গ্রাহক তথ্য দেখা',
                'orders.status' => 'অর্ডার স্ট্যাটাস পরিবর্তন ও চালান প্রিন্ট',
                'orders.delete' => 'অর্ডার ডিলিট বা মুছে ফেলা',
            ],
            'পণ্য ও স্টক (Products & Catalog)' => [
                'products.view'     => 'পণ্য ও স্টক রিপোর্ট দেখা',
                'products.create'   => 'নতুন পণ্য যুক্ত করা',
                'products.edit'     => 'পণ্যের মূল্য, ছবি ও তথ্য এডিট করা',
                'products.delete'   => 'পণ্য মুছে ফেলা',
                'categories.manage' => 'ক্যাটেগরি তৈরি ও সাজানো',
            ],
            'ফাইন্যান্স ও রাজস্ব (Finance & Revenue)' => [
                'finance.view' => 'আয়-ব্যয়, রাজস্ব ও আর্থিক স্ট্যাটিস্টিক রিপোর্ট দেখা',
            ],
            'মার্কেটিং ও ইন্টিগ্রেশন (Marketing & Integrations)' => [
                'banners.manage'      => 'ব্যানার স্লাইডার পরিবর্তন করা',
                'coupons.manage'      => 'ডিসকাউন্ট কুপন তৈরি ও নিয়ন্ত্রণ',
                'integrations.manage' => 'পিক্সেল, ট্যাগ ম্যানেজার ও ট্র্যাকিং স্ক্রিপ্টস ইন্টিগ্রেশন',
            ],
            'ইউজার ও স্টাফ ব্যবস্থাপনা (Staff & Users)' => [
                'users.view'   => 'ইউজার ও স্টাফদের তালিকা দেখা',
                'users.manage' => 'নতুন স্টাফ তৈরি, রোল ও পারমিশন এসাইন করা',
            ],
            'সিস্টেম ও সাইট সেটিংস (Settings & Branding)' => [
                'settings.manage' => 'সাইটের নাম, লোগো, পেমেন্ট ও থিম কালার পরিবর্তন',
            ],
        ];
    }

    /**
     * Flat key-value pair of all permissions.
     */
    public static function getAllPermissionsList(): array
    {
        $list = [];
        foreach (self::getAllPermissionsGrouped() as $group => $perms) {
            foreach ($perms as $key => $title) {
                $list[$key] = $title;
            }
        }
        return $list;
    }
}
