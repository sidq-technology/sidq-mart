@extends('layouts.app')

@section('title', 'প্রাইভেসি পলিসি | ' . \App\Models\Setting::get('site_name', 'SIDQ MART'))

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
                        <i class="fas fa-shield-halved fa-2x"></i>
                    </span>
                </div>
                <h1 class="fw-bold text-dark fs-2 mb-2">প্রাইভেসি পলিসি (Privacy Policy)</h1>
                <p class="text-muted small mb-0">সর্বশেষ আপডেট: {{ date('F Y') }} | আপনার ব্যক্তিগত তথ্যের নিরাপত্তা আমাদের সর্বোচ্চ অগ্রাধিকার</p>
            </div>

            <!-- Policy Content Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white policy-content lh-lg">
                
                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-info-circle text-danger me-2"></i>১. ভূমিকা
                    </h4>
                    <p class="text-secondary">
                        <strong>{{ $siteName }}</strong>-এ আপনাকে স্বাগতম। আমাদের ওয়েবসাইট ও সেবা ব্যবহারের সময় আপনার ব্যক্তিগত তথ্যের গোপনীয়তা রক্ষা করা আমাদের অন্যতম প্রধান দায়িত্ব। এই প্রাইভেসি পলিসিতে বিস্তারিতভাবে উল্লেখ করা হয়েছে আমরা কীভাবে আপনার তথ্য সংগ্রহ, সংরক্ষণ ও ব্যবহার করি।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-user-check text-danger me-2"></i>২. আমরা যে তথ্যগুলো সংগ্রহ করি
                    </h4>
                    <p class="text-secondary">একটি অর্ডার সফলভাবে সম্পন্ন করার জন্য গ্রাহকের কাছ থেকে আমরা নিম্নলিখিত তথ্যসমূহ সংগ্রহ করে থাকি:</p>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1"><strong>ব্যক্তিগত তথ্য:</strong> নাম, মোবাইল নম্বর এবং বিকল্প ফোন নম্বর।</li>
                        <li class="mb-1"><strong>ডেলিভারি তথ্য:</strong> সম্পূর্ণ ডেলিভারি ঠিকানা, জেলা, থানা এবং এলাকা।</li>
                        <li class="mb-1"><strong>অর্ডার ও ট্রানজেকশন তথ্য:</strong> অর্ডারের বিবরণ, পছন্দের পেমেন্ট মেথড (যেমন: ক্যাশ অন ডেলিভারি, বিকাশ বা নগদ)।</li>
                        <li class="mb-1"><strong>প্রযুক্তিগত তথ্য:</strong> আইপি অ্যাড্রেস (IP Address), ব্রাউজার টাইপ এবং ভিজিট সংক্রান্ত মৌলিক ট্র্যাকিং তথ্য যা ওয়েবসাইটের সিকিউরিটি ও সার্ভিস উন্নত করতে ব্যবহৃত হয়।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-cogs text-danger me-2"></i>৩. তথ্যের ব্যবহার
                    </h4>
                    <p class="text-secondary">সংগৃহীত তথ্য আমরা শুধুমাত্র নিচের উদ্দেশ্যে ব্যবহার করি:</p>
                    <ul class="text-secondary ps-3">
                        <li class="mb-1">অর্ডার প্রসেসিং, প্যাকিং এবং আপনার ঠিকানায় সঠিক সময়ে পণ্য পৌঁছে দেওয়ার জন্য।</li>
                        <li class="mb-1">অর্ডার কনফার্মেশন, ডেলিভারি আপডেট বা কোনো প্রয়োজনে আপনার সাথে ফোনে যোগাযোগ করার জন্য।</li>
                        <li class="mb-1">গ্রাহক সেবা নিশ্চিতকরণ এবং কোনো সমস্যা বা অভিযোগের দ্রুত সমাধান দেওয়ার জন্য।</li>
                        <li class="mb-1">সাইটে কোনো সন্দেহজনক বা ফেক অর্ডার সনাক্তকরণ ও ওয়েবসাইটের নিরাপত্তা বজায় রাখার স্বার্থে।</li>
                    </ul>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-lock text-danger me-2"></i>৪. তথ্যের নিরাপত্তা ও সুরক্ষার নিশ্চয়তা
                    </h4>
                    <p class="text-secondary">
                        আমরা দৃঢ়ভাবে অঙ্গীকার করছি যে, আপনার কোনো ব্যক্তিগত তথ্য বা মোবাইল নম্বর কোনো তৃতীয় পক্ষের কাছে বিক্রি, ভাড়া বা বাণিজ্যিক উদ্দেশ্যে হস্তান্তর করা হয় না। আপনার সকল তথ্য সুরক্ষিত ডেটাবেজে সম্পূর্ণ এনক্রিপ্টেড ও সংরক্ষিত থাকে। শুধুমাত্র পার্সেল ডেলিভারির স্বার্থে আমাদের অনুমোদিত কুরিয়ার পার্টনারকে (যেমন: ডেলিভারি রাইডার) আপনার নাম, ঠিকানা ও ফোন নম্বর প্রদান করা হয়।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-cookie-bite text-danger me-2"></i>৫. কুকিজ (Cookies) ও ব্রাউজার স্টোরেজ
                    </h4>
                    <p class="text-secondary">
                        আমাদের ওয়েবসাইটে আপনার ব্রাউজিং অভিজ্ঞতা আরও দ্রুত ও সহজ করতে এবং পরবর্তী অর্ডারের সময় তথ্য স্বয়ংক্রিয়ভাবে ফিল্ডে আনার সুবিধার জন্য কুকিজ বা লোকাল স্টোরেজ ব্যবহার করা হতে পারে। আপনি চাইলে আপনার ব্রাউজার সেটিংস থেকে যেকোনো সময় কুকিজ ডিজেবল করতে পারেন।
                    </p>
                </section>

                <section class="mb-4">
                    <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-headset text-danger me-2"></i>৬. যোগাযোগ ও কাস্টমার সাপোর্ট
                    </h4>
                    <p class="text-secondary">এই প্রাইভেসি পলিসি বা আপনার ব্যক্তিগত তথ্য সংক্রান্ত কোনো প্রশ্ন, মতামত বা জিজ্ঞাসা থাকলে নির্দ্বিধায় আমাদের সাথে যোগাযোগ করুন:</p>
                    <div class="p-3 bg-light rounded-3 border small">
                        <div class="mb-1"><strong>প্রতিষ্ঠান:</strong> {{ $siteName }}</div>
                        <div class="mb-1"><strong>হটলাইন:</strong> <a href="tel:{{ $contactPhone }}" class="text-danger text-decoration-none fw-semibold">{{ $contactPhone }}</a></div>
                        @if($contactEmail)
                        <div class="mb-1"><strong>ইমেইল:</strong> <a href="mailto:{{ $contactEmail }}" class="text-secondary text-decoration-none">{{ $contactEmail }}</a></div>
                        @endif
                        @if($contactAddress)
                        <div><strong>অফিস ঠিকানা:</strong> {{ $contactAddress }}</div>
                        @endif
                    </div>
                </section>

            </div>

        </div>
    </div>
</div>
@endsection
