@extends('layouts.app')

@section('title', '৪০৪ - পেজটি খুঁজে পাওয়া যায়নি | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@section('content')
<div class="container py-5 my-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 text-center">
            
            <!-- 404 Graphic Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-sm-5 bg-white">
                
                <!-- Animated / Stylized Illustration -->
                <div class="position-relative d-inline-block mx-auto mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle shadow-xs" style="width: 110px; height: 110px;">
                        <i class="fas fa-ghost fa-3x" style="color: var(--color-brand-accent, #f13124);"></i>
                    </div>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm px-3 py-2 fs-6 fw-bold">
                        404
                    </span>
                </div>

                <!-- Headings -->
                <h1 class="fw-bold text-dark mb-2 fs-2 fs-md-1">
                    দুঃখিত! পেজটি খুঁজে পাওয়া যায়নি
                </h1>
                <p class="text-muted fs-6 mb-4 mx-auto" style="max-width: 520px; line-height: 1.6;">
                    আপনি যে পেজটি বা পণ্যটি খুঁজছেন তা হয়তো মুছে ফেলা হয়েছে, নাম পরিবর্তন করা হয়েছে অথবা লিঙ্কটিতে কোনো ভুল রয়েছে।
                </p>

                <!-- In-page Search Box -->
                <div class="mx-auto mb-4 w-100" style="max-width: 480px;">
                    <form action="{{ route('search') }}" method="GET" class="position-relative shadow-xs rounded-pill overflow-hidden border">
                        <input type="text" name="q" class="form-control form-control-lg border-0 ps-4 pe-5 fs-6" placeholder="আপনি কি কিছু খুঁজছেন? এখানে সার্চ করুন..." style="height: 50px;">
                        <button type="submit" class="btn btn-danger position-absolute end-0 top-0 bottom-0 px-4 rounded-pill border-0 d-flex align-items-center justify-content-center" style="background-color: var(--color-brand-accent, #f13124);" aria-label="Search">
                            <i class="fas fa-search text-white"></i>
                        </button>
                    </form>
                </div>

                <!-- Primary Action Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-2 gap-sm-3 mb-4">
                    <a href="{{ route('home') }}" class="btn btn-primary-sidq py-2.5 px-4 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-xs">
                        <i class="fas fa-home"></i>
                        <span>হোম পেজে যান</span>
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-dark py-2.5 px-4 rounded-pill fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fas fa-shopping-bag"></i>
                        <span>সকল পণ্য দেখুন</span>
                    </a>
                    @php
                        $waNumber = \App\Models\Setting::get('whatsapp_number', \App\Models\Setting::get('contact_phone', '01711223344'));
                        $waClean = preg_replace('/[^0-9]/', '', $waNumber);
                        if (!str_starts_with($waClean, '88') && strlen($waClean) === 11) {
                            $waClean = '88' . $waClean;
                        }
                    @endphp
                    @if($waClean)
                    <a href="https://wa.me/{{ $waClean }}?text={{ urlencode('হ্যালো! সাইটে একটি পেজ খুঁজতে গিয়ে ৪০৪ এরর পাচ্ছি। সাহায্য প্রয়োজন।') }}" target="_blank" class="btn btn-outline-success py-2.5 px-3 rounded-pill fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fab fa-whatsapp fs-5"></i>
                        <span>সাহায্য নিন</span>
                    </a>
                    @endif
                </div>

                <!-- Quick Category Links -->
                @php
                    $popularCats = \App\Models\Category::where('is_active', true)->where('is_top', true)->take(6)->get();
                    if ($popularCats->isEmpty()) {
                        $popularCats = \App\Models\Category::where('is_active', true)->take(6)->get();
                    }
                @endphp
                @if($popularCats->isNotEmpty())
                <div class="border-top pt-4 mt-2">
                    <div class="small fw-bold text-muted mb-2">জনপ্রিয় ক্যাটাগরিগুলো ঘুরে দেখতে পারেন:</div>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        @foreach($popularCats as $cat)
                        <a href="{{ route('product.category', $cat->slug) }}" class="badge bg-light text-dark border text-decoration-none py-2 px-3 rounded-pill fw-medium transition-hover">
                            {{ $cat->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</div>
@endsection
