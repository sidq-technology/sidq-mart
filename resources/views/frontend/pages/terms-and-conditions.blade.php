@extends('layouts.app')

@section('title', 'শর্তাবলী ও নিয়মাবলী | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

@php
    $siteName = \App\Models\Setting::get('site_name', 'SIDQ MART');
    $contactPhone = \App\Models\Setting::get('contact_phone', '01711223344');
    $contactEmail = \App\Models\Setting::get('contact_email', 'support@sidqmart.com');
    $contactAddress = \App\Models\Setting::get('contact_address', 'Dhaka, Bangladesh');
@endphp

@section('content')
<div class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            
            <!-- Header Banner -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4 bg-white text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 64px; height: 64px;">
                        <i class="fas fa-file-contract fa-2x"></i>
                    </span>
                </div>
                <h1 class="fw-bold text-dark fs-2 mb-2">শর্তাবলী ও নীতিমালা (Terms & Conditions)</h1>
                <p class="text-muted small mb-0">সর্বশেষ আপডেট: {{ date('F Y') }} | {{ $siteName }} প্ল্যাটফর্ম ব্যবহারের সাধারণ নিয়মাবলী</p>
            </div>

            <!-- Terms Content Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white policy-content lh-lg">
                
                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-gavel text-danger me-2"></i>১. সাধারণ শর্তাবলী
                    </h4>
                    <p class="text-secondary">
                        <strong>{{ $siteName }}</strong> ওয়েবসাইটে ভিজিট করা বা কোনো পণ্য অর্ডার করার মাধ্যমে আপনি আমাদের এই শর্তাবলী ও নিয়মনীতি সম্পূর্ণভাবে মেনে নিতে সম্মত হচ্ছেন। আপনি যদি এই শর্তাবলীর কোনো অংশের সাথে একমত না হন, তবে ওয়েবসাইট ব্যবহার বা অর্ডার না করার অনুরোধ করা হলো।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-tags text-danger me-2"></i>২. পণ্যের মূল্য ও বিবরণ
                    </h4>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1">আমরা সকল পণ্যের বিবরণ, ছবি ও সঠিক মূল্য প্রদর্শনের সর্বোচ্চ চেষ্টা করি। তবে আলোকসজ্জা ও ডিসপ্লে ডিভাইসের পার্থক্যের কারণে বাস্তব পণ্যের রঙে সামান্য তারতম্য দেখা যেতে পারে।</li>
                        <li class="mb-1">বাজারের পরিস্থিতির ওপর নির্ভর করে কোনো পণ্যের স্টক বা মূল্য পূর্ব নোটিশ ছাড়াই পরিবর্তিত হতে পারে। তবে অর্ডার কনফার্ম হয়ে গেলে অর্ডারের মূল্যে কোনো পরিবর্তন হবে না।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-box-archive text-danger me-2"></i>৩. অর্ডার প্লেসমেন্ট ও কনফার্মেশন
                    </h4>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1">অর্ডার করার সময় গ্রাহককে অবশ্যই সঠিক নাম, সক্রিয় মোবাইল নম্বর এবং বিস্তারিত ঠিকানা প্রদান করতে হবে।</li>
                        <li class="mb-1">অর্ডার প্লেস করার পর আমাদের কাস্টমার সার্ভিস প্রতিনিধি ফোন কল বা এসএমএসের মাধ্যমে অর্ডার যাচাই করতে পারেন।</li>
                        <li class="mb-1">ভুল তথ্য, ভুয়া নম্বর বা অনাকাঙ্ক্ষিত কোনো কারিগরি ত্রুটির ক্ষেত্রে {{ $siteName }} কর্তৃপক্ষ যেকোনো অর্ডার বাতিল করার অধিকার সংরক্ষণ করে।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-truck-fast text-danger me-2"></i>৪. ডেলিভারি ও পরিবহন নীতিমালা
                    </h4>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1">ঢাকার ভিতরে সাধারণত ১ থেকে ২ কর্মদিবস এবং ঢাকার বাইরে ২ থেকে ৪ কর্মদিবসের মধ্যে ডেলিভারি সম্পন্ন করা হয়।</li>
                        <li class="mb-1">প্রাকৃতিক দুর্যোগ, রাজনৈতিক অস্থিরতা বা কুরিয়ারের অনিচ্ছাকৃত বিলম্বের ক্ষেত্রে ডেলিভারি কিছুটা বিলম্বিত হতে পারে, যা গ্রাহককে জানিয়ে দেওয়া হবে।</li>
                        <li class="mb-1">ক্যাশ অন ডেলিভারির ক্ষেত্রে ডেলিভারি ম্যানের সামনে পার্সেলটি চেক করে মূল্য পরিশোধ করার অনুরোধ জানানো হচ্ছে।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-hand-holding-dollar text-danger me-2"></i>৫. পেমেন্ট ও লেনদেন
                    </h4>
                    <p class="text-secondary">
                        আমরা ক্যাশ অন ডেলিভারি (পণ্য হাতে পেয়ে মূল্য পরিশোধ) এবং অনুমোদিত মোবাইল ব্যাংকিং (যেমন: বিকাশ/নগদ) সমর্থন করি। পেমেন্ট করার পর ট্রানজেকশন তথ্য বা স্লিপ সংরক্ষণ করার অনুরোধ করা হচ্ছে।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-phone-volume text-danger me-2"></i>৬. যোগাযোগ
                    </h4>
                    <p class="text-secondary">শর্তাবলী সংক্রান্ত যেকোনো ব্যাখ্যা বা প্রয়োজনে আমাদের হটলাইনে যোগাযোগ করতে পারেন:</p>
                    <div class="p-3 bg-light rounded-3 border small">
                        <div class="mb-1"><strong>হটলাইন:</strong> <a href="tel:{{ $contactPhone }}" class="text-danger text-decoration-none fw-semibold">{{ $contactPhone }}</a></div>
                        @if($contactEmail)
                        <div class="mb-1"><strong>ইমেইল:</strong> <a href="mailto:{{ $contactEmail }}" class="text-secondary text-decoration-none">{{ $contactEmail }}</a></div>
                        @endif
                        @if($contactAddress)
                        <div><strong>অফিস:</strong> {{ $contactAddress }}</div>
                        @endif
                    </div>
                </section>

            </div>

        </div>
    </div>
</div>
@endsection
