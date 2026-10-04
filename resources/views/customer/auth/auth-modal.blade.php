<!-- Customer Authentication Popup Modal (SIDQ Commerce Engine) -->
<div class="modal fade" id="customerAuthModal" tabindex="-1" aria-labelledby="customerAuthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header with Tabs -->
            <div class="modal-header border-0 pb-0 bg-white">
                <div class="w-100 d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle p-2 d-inline-flex align-items-center justify-content-center text-white" style="background: var(--color-brand-accent, #f13124); width: 34px; height: 34px;">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="customerAuthModalLabel">
                            {{ \App\Models\Setting::get('site_name', 'SIDQ MART') }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="px-4 pt-1 bg-white">
                <ul class="nav nav-pills nav-fill p-1 rounded-3 auth-nav-tabs" id="authTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-2 rounded-2" id="login-tab" data-bs-toggle="pill" data-bs-target="#modal-login-pane" type="button" role="tab" aria-selected="true">
                            <i class="fas fa-sign-in-alt me-1"></i> লগইন (Login)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-2 rounded-2" id="register-tab" data-bs-toggle="pill" data-bs-target="#modal-register-pane" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-user-plus me-1"></i> নতুন অ্যাকাউন্ট (Register)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 pt-3">
                <!-- Alert Message Box -->
                <div id="authModalAlert" class="alert d-none py-2 px-3 small rounded-3 mb-3"></div>

                <div class="tab-content" id="authTabContent">
                    <!-- 1. LOGIN FORM -->
                    <div class="tab-pane fade show active" id="modal-login-pane" role="tabpanel">
                        <form id="ajaxLoginForm" action="{{ route('login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">ইমেইল অথবা মোবাইল নম্বর <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                                    <input type="text" name="login" class="form-control border-start-0" placeholder="017XXXXXXXX বা example@domain.com" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">পাসওয়ার্ড <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="modalRememberMe" checked>
                                    <label class="form-check-label small text-muted" for="modalRememberMe">মনে রাখুন</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary-sidq w-100 py-2 fw-bold" id="btnLoginSubmit">
                                <i class="fas fa-sign-in-alt me-1"></i> লগইন করুন
                            </button>
                        </form>
                    </div>

                    <!-- 2. REGISTER FORM -->
                    <div class="tab-pane fade" id="modal-register-pane" role="tabpanel">
                        <form id="ajaxRegisterForm" action="{{ route('register.submit') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark">আপনার পূর্ণ নাম <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="যেমন: মো: সাইফুল ইসলাম" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark">ইমেইল ঠিকানা <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="example@domain.com" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold small text-dark">মোবাইল নম্বর (ঐচ্ছিক)</label>
                                <input type="text" name="phone" class="form-control form-control-sm" placeholder="017XXXXXXXX">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">পাসওয়ার্ড তৈরি করুন <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-sm" placeholder="কমপক্ষে ৬ অক্ষর" minlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-primary-sidq w-100 py-2 fw-bold" id="btnRegisterSubmit">
                                <i class="fas fa-user-check me-1"></i> অ্যাকাউন্ট তৈরি করুন
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top bg-light py-2 px-4 justify-content-center">
                <small class="text-muted" style="font-size: 12px;">
                    সুরক্ষিত ও পরিচালিত <strong class="text-dark">SIDQ Technology</strong>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alertBox = document.getElementById('authModalAlert');

        function showAlert(msg, isSuccess = false) {
            if (!alertBox) return;
            alertBox.className = 'alert py-2 px-3 small rounded-3 mb-3 ' + (isSuccess ? 'alert-success' : 'alert-danger');
            alertBox.innerHTML = (isSuccess ? '<i class="fas fa-check-circle me-1"></i> ' : '<i class="fas fa-exclamation-circle me-1"></i> ') + msg;
            alertBox.classList.remove('d-none');
        }

        // Handle AJAX Login
        const loginForm = document.getElementById('ajaxLoginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('btnLoginSubmit');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> যাচাই করা হচ্ছে...';

                fetch(loginForm.action, {
                    method: 'POST',
                    body: new FormData(loginForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showAlert(data.message || 'লগইন সফল হয়েছে! রিডাইরেক্ট করা হচ্ছে...', true);
                        setTimeout(() => {
                            window.location.href = data.redirect || window.location.href;
                        }, 800);
                    } else {
                        showAlert(data.message || 'লগইন ব্যর্থ হয়েছে। তথ্য যাচাই করুন।', false);
                        btn.disabled = false;
                        btn.innerHTML = origText;
                    }
                })
                .catch(() => {
                    showAlert('সার্ভারের সাথে সংযোগ স্থাপন করা যায়নি। আবার চেষ্টা করুন।', false);
                    btn.disabled = false;
                    btn.innerHTML = origText;
                });
            });
        }

        // Handle AJAX Register
        const registerForm = document.getElementById('ajaxRegisterForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const btn = document.getElementById('btnRegisterSubmit');
                const origText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> অ্যাকাউন্ট তৈরি হচ্ছে...';

                fetch(registerForm.action, {
                    method: 'POST',
                    body: new FormData(registerForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showAlert(data.message || 'অ্যাকাউন্ট তৈরি সফল হয়েছে!', true);
                        setTimeout(() => {
                            window.location.href = data.redirect || window.location.href;
                        }, 800);
                    } else {
                        showAlert(data.message || 'তথ্য সঠিকভাবে পূরণ করুন।', false);
                        btn.disabled = false;
                        btn.innerHTML = origText;
                    }
                })
                .catch(() => {
                    showAlert('সার্ভার এরর। অনুগ্রহ করে আবার চেষ্টা করুন।', false);
                    btn.disabled = false;
                    btn.innerHTML = origText;
                });
            });
    });
</script>

<style>
.auth-nav-tabs {
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
}
.auth-nav-tabs .nav-link {
    color: #475569;
    font-size: 13.5px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: none;
}
.auth-nav-tabs .nav-link:hover {
    color: var(--color-brand-accent, #f13124);
}
.auth-nav-tabs .nav-link.active {
    background-color: var(--color-brand-accent, #f13124) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(241, 49, 36, 0.28) !important;
}
#customerAuthModal .form-check-input:checked {
    background-color: var(--color-brand-accent, #f13124);
    border-color: var(--color-brand-accent, #f13124);
}
#customerAuthModal .form-control:focus {
    border-color: var(--color-brand-accent, #f13124);
    box-shadow: 0 0 0 0.2rem rgba(241, 49, 36, 0.15);
}
</style>
