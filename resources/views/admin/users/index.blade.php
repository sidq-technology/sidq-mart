@extends('layouts.admin')

@section('title', 'ইউজার ও রোল ম্যানেজমেন্ট - Users & Staff')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-users-cog me-2" style="color: var(--admin-primary);"></i> ইউজার ও স্টাফ ম্যানেজমেন্ট (RBAC System)
        </h3>
        <p class="text-muted small mb-0">অ্যাডমিন, শপ ম্যানেজার, এমপ্লয়ি ও গ্রাহকদের রোল এবং কে কি কাজ করতে পারবে তা নিয়ন্ত্রণ করুন।</p>
    </div>
    <div>
        <button type="button" class="btn btn-admin-primary px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-1"></i> নতুন স্টাফ / ইউজার যোগ করুন
        </button>
    </div>
</div>

<!-- 1. User Summary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">মোট ব্যবহারকারী (Total)</span>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['total'] }} জন</h3>
                <small class="text-success"><i class="fas fa-check-circle me-1"></i> সকল অ্যাকাউন্ট</small>
            </div>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">সুপার অ্যাডমিন (Admins)</span>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['admins'] }} জন</h3>
                <small class="text-primary"><i class="fas fa-shield-alt me-1"></i> পূর্ণ নিয়ন্ত্রণ</small>
            </div>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">ম্যানেজার ও এমপ্লয়ি (Staff)</span>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['managers'] + $stats['employees'] }} জন</h3>
                <small class="text-warning fw-semibold"><i class="fas fa-user-tie me-1"></i> শপ ম্যানেজার: {{ $stats['managers'] }}, স্টাফ: {{ $stats['employees'] }}</small>
            </div>
            <div class="metric-icon" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card">
            <div>
                <span class="text-muted small fw-medium">রেজিস্টার্ড গ্রাহক (Customers)</span>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $stats['customers'] }} জন</h3>
                <small class="text-muted"><i class="fas fa-shopping-bag me-1"></i> স্টোর ক্রেতা</small>
            </div>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="fas fa-user-tag"></i>
            </div>
        </div>
    </div>
</div>

<!-- 2. Search & Role Filter Bar -->
<div class="card admin-surface-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="নাম, ইমেইল বা মোবাইল নম্বর দিয়ে খুঁজুন..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">সকল রোল (All Roles)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>সুপার অ্যাডমিন (Administrator)</option>
                    <option value="shop_manager" {{ request('role') === 'shop_manager' ? 'selected' : '' }}>শপ ম্যানেজার (Shop Manager)</option>
                    <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>এমপ্লয়ি / স্টাফ (Employee)</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>সাধারণ গ্রাহক (Customer)</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-admin-primary px-4">
                    <i class="fas fa-filter me-1"></i> ফিল্টার
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-1"></i> রিসেট
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- 3. Users List Table -->
<div class="card admin-surface-card">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fas fa-address-book me-2" style="color: var(--admin-primary);"></i> সকল ব্যবহারকারীর তালিকা ({{ $users->total() }})
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead>
                    <tr>
                        <th class="ps-4">#ID</th>
                        <th>ইউজার প্রোফাইল (User)</th>
                        <th>মোবাইল নম্বর</th>
                        <th>রোল / পদবী (Role)</th>
                        <th>অনুমতি / পারমিশন</th>
                        <th>অর্ডার হিস্ট্রি</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php $badge = $user->role_badge_style; @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-muted">#{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-circle" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; border: 2px solid {{ $badge['border'] }};">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">আপনি (You)</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted"><i class="far fa-envelope me-1"></i>{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->phone)
                                <span class="fw-medium text-dark"><i class="fas fa-phone-alt text-muted small me-1"></i>{{ $user->phone }}</span>
                            @else
                                <span class="text-muted small fst-italic">দেওয়া হয়নি</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-weight: 600;">
                                <i class="fas {{ $badge['icon'] }}"></i> {{ $badge['label'] }}
                            </span>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="fas fa-check-double me-1"></i> পূর্ণ অনুমতি (All Access)
                                </span>
                            @elseif($user->role === 'customer')
                                <span class="text-muted small"><i class="fas fa-store me-1"></i> স্টোরফ্রন্ট শপিং</span>
                            @else
                                @php
                                    $pCount = is_array($user->permissions) ? count($user->permissions) : count(\App\Models\User::getDefaultRolePermissions($user->role));
                                @endphp
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-key text-warning me-1"></i> {{ $pCount }} টি পারমিশন সক্রিয়
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $user->orders_count ?? 0 }} টি অর্ডার</div>
                            @if(($user->orders_sum_grand_total ?? 0) > 0)
                                <small class="text-success fw-medium">৳{{ number_format($user->orders_sum_grand_total, 0) }}</small>
                            @else
                                <small class="text-muted">৳০</small>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-2">
                                <button type="button"
                                        class="btn btn-sm btn-light border text-primary btn-edit-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-address="{{ $user->address }}"
                                        data-role="{{ $user->role }}"
                                        data-permissions='@json($user->permissions ?? \App\Models\User::getDefaultRolePermissions($user->role))'
                                        data-update-url="{{ route('admin.users.update', $user->id) }}"
                                        title="এডিট ও পারমিশন পরিবর্তন">
                                    <i class="fas fa-user-cog"></i> এডিট
                                </button>

                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই অ্যাকাউন্টটি ডিলিট করতে চান?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="ডিলিট করুন">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-sm btn-light border text-muted" disabled title="নিজের অ্যাকাউন্ট ডিলিট করা যাবে না">
                                    <i class="fas fa-lock"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fs-2 mb-2 d-block opacity-50"></i>
                            কোনো ইউজার পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-transparent border-top py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- ==========================================
     Create New Staff / User Modal
     ========================================== -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom" style="background: var(--admin-mint-bg);">
                <h5 class="modal-title fw-bold text-dark" id="createUserModalLabel">
                    <i class="fas fa-user-plus me-2" style="color: var(--admin-primary);"></i> নতুন ইউজার / স্টাফ তৈরি ও রোল এসাইন করুন
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">পূর্ণ নাম (Full Name) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="যেমন: মো: আব্দুল্লাহ" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">ইমেইল ঠিকানা (Email Address) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="example@domain.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">মোবাইল নম্বর (Phone)</label>
                            <input type="text" name="phone" class="form-control" placeholder="017XXXXXXXX">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">রোল নির্বাচন করুন (Role) <span class="text-danger">*</span></label>
                            <select name="role" id="create_user_role" class="form-select fw-semibold" required>
                                <option value="customer">সাধারণ গ্রাহক (Customer - শুধুমাত্র শপিং)</option>
                                <option value="employee">এমপ্লয়ি / স্টাফ (Employee - অর্ডার প্রসেসিং)</option>
                                <option value="shop_manager">শপ ম্যানেজার (Shop Manager - পণ্য ও অর্ডার)</option>
                                <option value="admin">সুপার অ্যাডমিন (Administrator - পূর্ণ ক্ষমতা)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">পাসওয়ার্ড (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="ন্যূনতম ৬ অক্ষরের পাসওয়ার্ড" minlength="6" required>
                    </div>

                    <!-- Granular Permission Checklist Box -->
                    <div class="p-3 rounded-3 border" id="create_permission_box" style="background: var(--admin-mint-bg);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small">
                                <i class="fas fa-user-shield me-1" style="color: var(--admin-primary);"></i> অ্যাক্সেস পারমিশনসমূহ (কে কি কাজ করতে পারবে):
                            </span>
                            <span class="badge bg-light text-muted border" id="create_role_preset_badge">ডিফল্ট প্রিসেট</span>
                        </div>
                        <p class="text-muted small mb-3" id="create_permission_hint">
                            নিচে টিক চিহ্ন দিয়ে এই স্টাফকে নির্দিষ্ট কাজের অনুমতি প্রদান করুন।
                        </p>

                        <div class="row g-3" id="create_permission_checks">
                            @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="col-12 col-md-6">
                                <div class="p-2 bg-white rounded-2 border h-100">
                                    <div class="fw-bold text-dark border-bottom pb-1 mb-2" style="font-size: 13px;">
                                        {{ $groupName }}
                                    </div>
                                    @foreach($perms as $key => $title)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input create-perm-check" type="checkbox" name="permissions[]" value="{{ $key }}" id="create_perm_{{ str_replace('.', '_', $key) }}">
                                        <label class="form-check-label small" for="create_perm_{{ str_replace('.', '_', $key) }}">
                                            {{ $title }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-admin-primary px-4 fw-bold">
                        <i class="fas fa-check me-1"></i> অ্যাকাউন্ট তৈরি করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     Edit Staff / User & Permissions Modal
     ========================================== -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom" style="background: var(--admin-mint-bg);">
                <h5 class="modal-title fw-bold text-dark" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2" style="color: var(--admin-primary);"></i> তথ্য, রোল ও পারমিশন পরিবর্তন করুন
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">পূর্ণ নাম (Full Name) <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_user_name" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">ইমেইল ঠিকানা (Email Address) <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_user_email" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">মোবাইল নম্বর (Phone)</label>
                            <input type="text" name="phone" id="edit_user_phone" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold">রোল নির্বাচন করুন (Role) <span class="text-danger">*</span></label>
                            <select name="role" id="edit_user_role" class="form-select fw-semibold" required>
                                <option value="customer">সাধারণ গ্রাহক (Customer - শুধুমাত্র শপিং)</option>
                                <option value="employee">এমপ্লয়ি / স্টাফ (Employee - অর্ডার প্রসেসিং)</option>
                                <option value="shop_manager">শপ ম্যানেজার (Shop Manager - পণ্য ও অর্ডার)</option>
                                <option value="admin">সুপার অ্যাডমিন (Administrator - পূর্ণ ক্ষমতা)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">নতুন পাসওয়ার্ড (ঐচ্ছিক)</label>
                        <input type="password" name="password" class="form-control" placeholder="পরিবর্তন না করতে চাইলে ফাঁকা রাখুন" minlength="6">
                        <div class="form-text small">পাসওয়ার্ড অপরিবর্তিত রাখতে এই ঘরটি ফাঁকা রাখুন।</div>
                    </div>

                    <!-- Edit Granular Permission Checklist Box -->
                    <div class="p-3 rounded-3 border" id="edit_permission_box" style="background: var(--admin-mint-bg);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark small">
                                <i class="fas fa-user-shield me-1" style="color: var(--admin-primary);"></i> অ্যাক্সেস পারমিশনসমূহ (কে কি কাজ করতে পারবে):
                            </span>
                        </div>

                        <div class="row g-3" id="edit_permission_checks">
                            @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="col-12 col-md-6">
                                <div class="p-2 bg-white rounded-2 border h-100">
                                    <div class="fw-bold text-dark border-bottom pb-1 mb-2" style="font-size: 13px;">
                                        {{ $groupName }}
                                    </div>
                                    @foreach($perms as $key => $title)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input edit-perm-check" type="checkbox" name="permissions[]" value="{{ $key }}" id="edit_perm_{{ str_replace('.', '_', $key) }}">
                                        <label class="form-check-label small" for="edit_perm_{{ str_replace('.', '_', $key) }}">
                                            {{ $title }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-admin-primary px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> আপডেট সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultRolePermissions = @json($defaultPermissions);

        // Function to apply preset checkboxes
        function applyRolePermissions(role, containerSelector) {
            const checks = document.querySelectorAll(containerSelector);
            const box = document.querySelector(containerSelector.replace('.create-perm-check', '#create_permission_box').replace('.edit-perm-check', '#edit_permission_box'));

            if (role === 'admin') {
                checks.forEach(c => { c.checked = true; c.disabled = true; });
                if (box) box.style.opacity = '0.7';
            } else if (role === 'customer') {
                checks.forEach(c => { c.checked = false; c.disabled = true; });
                if (box) box.style.opacity = '0.5';
            } else {
                const perms = defaultRolePermissions[role] || [];
                checks.forEach(c => {
                    c.disabled = false;
                    c.checked = perms.includes(c.value);
                });
                if (box) box.style.opacity = '1';
            }
        }

        // Create Modal Role Change Listener
        const createRoleSelect = document.getElementById('create_user_role');
        if (createRoleSelect) {
            createRoleSelect.addEventListener('change', function () {
                applyRolePermissions(this.value, '.create-perm-check');
            });
            // Initial call
            applyRolePermissions(createRoleSelect.value, '.create-perm-check');
        }

        // Edit Modal Setup
        const editButtons = document.querySelectorAll('.btn-edit-user');
        const editForm = document.getElementById('editUserForm');
        const nameInput = document.getElementById('edit_user_name');
        const emailInput = document.getElementById('edit_user_email');
        const phoneInput = document.getElementById('edit_user_phone');
        const editRoleSelect = document.getElementById('edit_user_role');
        const editModalEl = document.getElementById('editUserModal');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                editForm.action = this.dataset.updateUrl;
                nameInput.value = this.dataset.name || '';
                emailInput.value = this.dataset.email || '';
                phoneInput.value = this.dataset.phone || '';
                const role = this.dataset.role || 'customer';
                editRoleSelect.value = role;

                let userPerms = [];
                try {
                    userPerms = JSON.parse(this.dataset.permissions || '[]');
                } catch (e) {
                    userPerms = defaultRolePermissions[role] || [];
                }

                const checks = document.querySelectorAll('.edit-perm-check');
                if (role === 'admin') {
                    checks.forEach(c => { c.checked = true; c.disabled = true; });
                } else if (role === 'customer') {
                    checks.forEach(c => { c.checked = false; c.disabled = true; });
                } else {
                    checks.forEach(c => {
                        c.disabled = false;
                        c.checked = userPerms.includes(c.value);
                    });
                }

                const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                modal.show();
            });
        });

        if (editRoleSelect) {
            editRoleSelect.addEventListener('change', function () {
                applyRolePermissions(this.value, '.edit-perm-check');
            });
        }
    });
</script>
@endpush
