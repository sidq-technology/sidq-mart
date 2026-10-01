<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of all users (Admins, Managers, Employees, Customers).
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount('orders')
            ->withSum('orders', 'grand_total');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if (in_array($role, [User::ROLE_ADMIN, User::ROLE_SHOP_MANAGER, User::ROLE_EMPLOYEE, User::ROLE_DEMO_ADMIN, User::ROLE_CUSTOMER])) {
                $query->where('role', $role);
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => User::count(),
            'admins'    => User::where('role', User::ROLE_ADMIN)->count(),
            'managers'  => User::where('role', User::ROLE_SHOP_MANAGER)->count(),
            'employees' => User::where('role', User::ROLE_EMPLOYEE)->count(),
            'demos'     => User::where('role', User::ROLE_DEMO_ADMIN)->count(),
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
        ];

        $permissionsGrouped = User::getAllPermissionsGrouped();
        $defaultPermissions = [
            User::ROLE_ADMIN        => array_keys(User::getAllPermissionsList()),
            User::ROLE_SHOP_MANAGER => User::getDefaultRolePermissions(User::ROLE_SHOP_MANAGER),
            User::ROLE_EMPLOYEE     => User::getDefaultRolePermissions(User::ROLE_EMPLOYEE),
            User::ROLE_DEMO_ADMIN   => array_keys(User::getAllPermissionsList()),
            User::ROLE_CUSTOMER     => [],
        ];

        return view('admin.users.index', compact('users', 'stats', 'permissionsGrouped', 'defaultPermissions'));
    }

    /**
     * Store a newly created user or staff member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:500'],
            'role'        => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SHOP_MANAGER, User::ROLE_EMPLOYEE, User::ROLE_DEMO_ADMIN, User::ROLE_CUSTOMER])],
            'permissions' => ['nullable', 'array'],
            'password'    => ['required', 'string', 'min:6'],
        ]);

        $permissions = null;
        if (in_array($validated['role'], [User::ROLE_SHOP_MANAGER, User::ROLE_EMPLOYEE])) {
            $raw = $request->input('permissions');
            if (is_array($raw)) {
                $validKeys = array_keys(User::getAllPermissionsList());
                $permissions = array_values(array_intersect($raw, $validKeys));
            }
            if (empty($permissions)) {
                $permissions = User::getDefaultRolePermissions($validated['role']);
            }
        }

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'address'     => $validated['address'] ?? null,
            'role'        => $validated['role'],
            'permissions' => $permissions,
            'password'    => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'নতুন ' . (new User(['role' => $validated['role']]))->role_title . ' সফলভাবে যুক্ত করা হয়েছে।');
    }

    /**
     * Update an existing user's profile, role, or granular permissions.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'       => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:500'],
            'role'        => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SHOP_MANAGER, User::ROLE_EMPLOYEE, User::ROLE_DEMO_ADMIN, User::ROLE_CUSTOMER])],
            'permissions' => ['nullable', 'array'],
            'password'    => ['nullable', 'string', 'min:6'],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== User::ROLE_ADMIN) {
            return redirect()->back()
                ->with('error', 'নিরাপত্তার স্বার্থে আপনি নিজের অ্যাডমিন রোল পরিবর্তন করতে পারবেন না।');
        }

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->phone   = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->role    = $validated['role'];

        if (in_array($validated['role'], [User::ROLE_SHOP_MANAGER, User::ROLE_EMPLOYEE])) {
            $raw = $request->input('permissions');
            if (is_array($raw)) {
                $validKeys = array_keys(User::getAllPermissionsList());
                $permissions = array_values(array_intersect($raw, $validKeys));
                $user->permissions = !empty($permissions) ? $permissions : User::getDefaultRolePermissions($validated['role']);
            } else {
                $user->permissions = User::getDefaultRolePermissions($validated['role']);
            }
        } elseif ($validated['role'] === User::ROLE_ADMIN) {
            $user->permissions = null; // Admin has all permissions implicitly
        } else {
            $user->permissions = null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'ইউজারের তথ্য ও রোল পারমিশন সফলভাবে আপডেট করা হয়েছে।');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'আপনি নিজের লগইনকৃত অ্যাডমিন অ্যাকাউন্ট ডিলিট করতে পারবেন না।');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ইউজার সফলভাবে ডিলিট করা হয়েছে।');
    }
}
