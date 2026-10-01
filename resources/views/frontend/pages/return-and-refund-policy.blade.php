@extends('layouts.app')

@section('title', 'রিটার্ন ও রিফান্ড পলিসি | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

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
                        <i class="fas fa-arrow-rotate-left fa-2x"></i>
                    </span>
                </div>
                <h1 class="fw-bold text-dark fs-2 mb-2">রিটার্ন ও রিফান্ড পলিসি (Return & Refund Policy)</h1>
                <p class="text-muted small mb-0">সর্বশেষ আপডেট: {{ date('F Y') }} | গ্রাহকের ১০০% সন্তুষ্টি আমাদের প্রধান লক্ষ্য</p>
            </div>

            <!-- Return Policy Content Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white policy-content lh-lg">
                
                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-shield-alt text-danger me-2"></i>১. আমাদের রিটার্ন প্রতিশ্রুতি
                    </h4>
                    <p class="text-secondary">
                        <strong>{{ $siteName }}</strong> থেকে কেনাকাটায় গ্রাহকের শতভাগ সন্তুষ্টি নিশ্চিত করাই আমাদের লক্ষ্য। পণ্য গ্রহণের পর যদি কোনো ধরনের সমস্যা, ত্রুটি বা অমিল পাওয়া যায়, তবে আপনি সহজেই আমাদের রিটার্ন ও রিফান্ড সুবিধা উপভোগ করতে পারবেন।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-calendar-check text-danger me-2"></i>২. রিটার্নের সময়সীমা
                    </h4>
                    <p class="text-secondary">
                        পণ্যটি হাতে পাওয়ার পর সর্বোচ্চ <strong>৭ (সাত) দিনের মধ্যে</strong> আমাদের হেল্পলাইন বা ফেসবুক পেজে রিটার্নের জন্য অবগত করতে হবে। সাত দিন অতিক্রান্ত হওয়ার পর কোনো রিটার্ন বা রিফান্ড রিকোয়েস্ট গ্রহণযোগ্য হবে না।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-check-circle text-danger me-2"></i>৩. যে সকল ক্ষেত্রে রিটার্ন গ্রহণযোগ্য
                    </h4>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1"><strong>ভাঙা বা নষ্ট পণ্য:</strong> ডেলিভারির সময় পণ্য ক্ষতিগ্রস্ত বা ত্রুটিপূর্ণ অবস্থায় পাওয়া গেলে।</li>
                        <li class="mb-1"><strong>ভুল পণ্য ডেলিভারি:</strong> অর্ডারের সাথে ডেলিভারিকৃত পণ্যের কালার, সাইজ বা মডেল অমিল হলে।</li>
                        <li class="mb-1"><strong>অপ্রত্যাশিত ফাংশনাল ত্রুটি:</strong> গ্যাজেট বা ইলেকট্রনিক পণ্যের ক্ষেত্রে পণ্যটি চালু না হলে বা কাজ না করলে।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-box-open text-danger me-2"></i>৪. রিটার্নের সাধারণ শর্তাবলী
                    </h4>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1">পণ্যটি অবশ্যই অব্যবহৃত (Unused) এবং মূল প্যাকেজিং, ইনভয়েস বা ডেলিভারি স্লিপসহ অক্ষত থাকতে হবে।</li>
                        <li class="mb-1">ত্রুটিযুক্ত পণ্যের ছবি বা আনবক্সিং ভিডিও প্রমাণ হিসেবে আমাদের প্রতিনিধিকে প্রদান করতে হবে।</li>
                        <li class="mb-1">গ্রাহকের ব্যক্তিগত পছন্দ পরিবর্তন বা ইচ্ছাকৃত ক্ষতির কারণে কোনো পণ্য রিটার্ন নেওয়া হবে না।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-money-bill-transfer text-danger me-2"></i>৫. রিফান্ড প্রক্রিয়া ও সময়সীমা
                    </h4>
                    <p class="text-secondary">
                        রিটার্নকৃত পণ্য আমাদের অফিসে পৌঁছানোর পর কোয়ালিটি চেকিং করা হয়। কোয়ালিটি চেক সফলভাবে সম্পন্ন হওয়ার <strong>২ থেকে ৫ কার্যদিবসের মধ্যে</strong> আপনার কাঙ্ক্ষিত রিফান্ড (বিকাশ, নগদ বা ব্যাংক একাউন্টের মাধ্যমে) সম্পন্ন করা হবে।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-truck-pickup text-danger me-2"></i>৬. ডেলিভারি ও রিটার্ন চার্জ
                    </h4>
                    <p class="text-secondary">
                        যদি আমাদের ভুলের কারণে (ভুল পণ্য বা নষ্ট পণ্য) রিটার্ন হয়, তবে রিটার্ন ডেলিভারি চার্জ সম্পূর্ণ <strong>{{ $siteName }}</strong> বহন করবে। গ্রাহকের ভুলের কারণে রিটার্ন বা এক্সচেঞ্জ করতে চাইলে কুরিয়ার ডেলিভারি চার্জ গ্রাহককে বহন করতে হবে।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-headset text-danger me-2"></i>৭. রিটার্ন কীভাবে করবেন?
                    </h4>
                    <p class="text-secondary">রিটার্ন বা রিফান্ড রিকোয়েস্টের জন্য অনুগ্রহ করে নিচের নম্বরে কল করুন বা যোগাযোগ করুন:</p>
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
