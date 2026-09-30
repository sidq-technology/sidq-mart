<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Core Platform Engineered & Powered by SIDQ Technology (সিদিক টেকনোলজি)
     */
    public function run(): void
    {
        // 1. Admin and Demo Customer (SIDQ Technology Credentials)
        $admin = User::updateOrCreate(
            ['email' => 'admin@sidqmart.com'],
            [
                'name' => 'SIDQ Technology Administrator',
                'phone' => '01700000000',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Alias for tech domain
        User::updateOrCreate(
            ['email' => 'admin@sidqtech.com'],
            [
                'name' => 'SIDQ Technology Support',
                'phone' => '01700000000',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@sidqmart.com'],
            [
                'name' => 'সাকিব হাসান (শপ ম্যানেজার)',
                'phone' => '01811223344',
                'role' => User::ROLE_SHOP_MANAGER,
                'password' => Hash::make('password123'),
                'permissions' => User::getDefaultRolePermissions(User::ROLE_SHOP_MANAGER),
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee@sidqmart.com'],
            [
                'name' => 'রাকিব ইসলাম (এমপ্লয়ি)',
                'phone' => '01711223355',
                'role' => User::ROLE_EMPLOYEE,
                'password' => Hash::make('password123'),
                'permissions' => User::getDefaultRolePermissions(User::ROLE_EMPLOYEE),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@sidqmart.com'],
            [
                'name' => 'তানভীর আহমেদ (গ্রাহক)',
                'phone' => '01911223366',
                'role' => User::ROLE_CUSTOMER,
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Settings (Dynamic website name, logo, payment information, delivery charges)
        $settings = [
            'site_name' => 'SIDQ MART',
            'site_slogan' => 'SIDQ MART — Online Shopping In Bangladesh | Powered by SIDQ Technology',
            'site_logo' => asset('images/sidq-mart-logo.svg'),
            'site_favicon' => asset('favicon.png'),
            'contact_phone' => '01711223344',
            'contact_email' => 'support@sidqmart.com',
            'whatsapp_number' => '01711223344',
            'contact_address' => 'Dhaka, Bangladesh',
            'currency_symbol' => '৳',
            'delivery_inside_dhaka' => '70',
            'delivery_outside_dhaka' => '130',
            'free_delivery_threshold' => '3000',
            
            // Payment Settings
            'cod_enabled' => '1',
            'cod_instructions' => 'ক্যাশ অন ডেলিভারি (পণ্য হাতে পেয়ে মূল্য পরিশোধ করুন)।',
            
            'bkash_enabled' => '1',
            'bkash_number' => '01711223344',
            'bkash_type' => 'Personal (Send Money)',
            'bkash_instructions' => 'দয়া করে указанный বিকাশ নম্বরে Send Money করুন এবং ট্রানজেকশন আইডি (TrxID) নিচে প্রদান করুন।',
            
            'nagad_enabled' => '1',
            'nagad_number' => '01711223344',
            'nagad_type' => 'Personal (Send Money)',
            'nagad_instructions' => 'দয়া করে указанный নগদ নম্বরে Send Money করুন এবং ট্রানজেকশন আইডি (TrxID) নিচে দিন।',

            'meta_pixel_id' => '1014535146791355',
            'footer_about' => 'SIDQ MART হলো সিদিক টেকনোলজি (SIDQ Technology) এর একটি আধুনিক ই-কমার্স প্ল্যাটফর্ম। আমরা সাশ্রয়ী মূল্যে সর্বোচ্চ মানের গ্যাজেট, কিচেন এবং গৃহস্থালি পণ্য সরবরাহ করি।',
            'notice_text' => 'সারা বাংলাদেশে ক্যাশ অন হোম ডেলিভারি সুবিধা! যে কোনো তথ্যের জন্য কল করুন: 01711223344',
        ];

        foreach ($settings as $key => $val) {
            Setting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 3. Categories (Clean local storage assets)
        $categoriesData = [
            [
                'name' => 'Gadget & Electronics',
                'slug' => 'gadget-electronics',
                'is_top' => true,
                'image' => 'categories/sidq-cat-gadgets.jpg',
            ],
            [
                'name' => 'Camera & Accessories',
                'slug' => 'camera',
                'is_top' => true,
                'image' => 'categories/sidq-cat-kitchen.jpg',
            ],
            [
                'name' => 'Kitchen Accessories',
                'slug' => 'kitchen-accessories',
                'is_top' => true,
                'image' => 'categories/sidq-cat-kitchen.jpg',
            ],
            [
                'name' => "Summer's Demand",
                'slug' => 'summers-demand',
                'is_top' => true,
                'image' => 'categories/sidq-cat-summer.webp',
            ],
            [
                'name' => 'Clothing Rack & Storage',
                'slug' => 'clothing-rack',
                'is_top' => true,
                'image' => 'categories/sidq-cat-rack.jpg',
            ],
            [
                'name' => 'Kitchen Shelf & Organizers',
                'slug' => 'kitchen-shelf',
                'is_top' => true,
                'image' => 'categories/sidq-cat-shelf.jpg',
            ],
            [
                'name' => 'Watch & Smartwatches',
                'slug' => 'watch',
                'is_top' => true,
                'image' => 'categories/sidq-cat-watch.jpg',
            ],
            [
                'name' => 'Bath & Cleaning',
                'slug' => 'bath-cleaning',
                'is_top' => true,
                'image' => 'categories/sidq-cat-cleaning.jpg',
            ],
        ];

        $categoryModels = [];
        foreach ($categoriesData as $index => $cat) {
            $categoryModels[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'is_top' => $cat['is_top'],
                    'sort_order' => $index,
                    'image' => $cat['image'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Products (Authentic items with local storage assets & Bengali details)
        $productsData = [
            [
                'name' => 'Breakfast Boiled Egg Mold - Set of 4 +1 Brush Free(🔥🔴 ক্যাশ অন ডেলিভারি 🔴 🔥)',
                'slug' => 'breakfast-boiled-egg-mold-set-of-4-1-brush-free',
                'category_slug' => 'kitchen-accessories',
                'regular_price' => 699,
                'sale_price' => 490,
                'stock_quantity' => 100,
                'is_featured' => true,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-egg-mold.webp',
                'short_description' => 'বাচ্চাদের ডিম খাওয়ার প্রতি আগ্রহ বাড়াতে আকর্ষণীয় ৪টি শেপের ডিম বয়েল্ড মোল্ড এবং সাথে ১টি ব্রাশ একদম ফ্রি!',
                'description' => '<p><strong>Breakfast Boiled Egg Mold:</strong></p><ul><li>৪ টি ভিন্ন ভিন্ন সুন্দর শেপ (স্টার, হার্ট, ফ্লাওয়ার ইত্যাদি)</li><li>ফুড গ্রেড পিপি প্লাস্টিক দ্বারা তৈরি</li><li>তাপ প্রতিরোধী এবং ব্যবহার করা অত্যন্ত সহজ</li><li>সাথে রয়েছে একটি সিলিকন অয়েল ব্রাশ সম্পূর্ণ ফ্রি</li></ul>',
                'specifications' => [
                    'Material' => 'Food Grade PP Plastic',
                    'Pieces' => '4 Molds + 1 Brush',
                    'Heat Resistance' => 'Up to 110°C',
                    'Origin' => 'China',
                ],
            ],
            [
                'name' => 'Rechargeable Fan with LED Light',
                'slug' => 'rechargeable-fan-with-led-light',
                'category_slug' => 'summers-demand',
                'regular_price' => 1990,
                'sale_price' => 1299,
                'stock_quantity' => 45,
                'is_featured' => true,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-fan.webp',
                'short_description' => 'লোডশেডিংয়ে আরামদায়ক বাতাসের জন্য পোর্টেবল রিচার্জেবল ফ্যান সাথে শক্তিশালী এলইডি লাইট।',
                'description' => '<p>শক্তিশালী ব্যাটারি ব্যাকআপ সহ মাল্টি-ফাংশন রিচার্জেবল ফ্যান। ইউএসবি চার্জিং সুবিধা এবং ফোল্ডেবল ডিজাইন।</p>',
                'specifications' => [
                    'Battery' => 'Lithium-ion Rechargeable',
                    'Speed' => '3 Speed Control',
                    'Charging' => 'Type-C / USB Fast Charging',
                    'Backup' => '3-5 Hours',
                ],
            ],
            [
                'name' => '🔥 Heat-resistant 3D gold foil dining placemat 🍽️🥰(3 pcs)(🔥 🔴 ক্যাশ অন ডেলিভারি 🔴 🔥)',
                'slug' => 'heat-resistant-3d-gold-foil-dining-placemat-3-pcs',
                'category_slug' => 'kitchen-accessories',
                'regular_price' => 1099,
                'sale_price' => 899,
                'stock_quantity' => 60,
                'is_featured' => true,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-placemat.webp',
                'short_description' => 'ডাইনিং টেবিলকে আকর্ষণীয় ও সুন্দর করতে ৩ পিসের লাক্সারি গোল্ড ফয়েল হিট রেজিস্ট্যান্ট প্লেসম্যাট।',
                'description' => '<p>ওয়াটারপ্রুফ, অয়েলপ্রুফ এবং সহজে পরিষ্কারযোগ্য প্রিমিয়াম ৩ডি গোল্ড ফয়েল প্লেসম্যাট সেট।</p>',
                'specifications' => [
                    'Quantity' => '3 Pieces',
                    'Material' => 'PVC Gold Foil',
                    'Feature' => 'Heat Resistant & Washable',
                ],
            ],
            [
                'name' => 'NEW Winter Protection Windproof Cap with Scarf',
                'slug' => 'new-winter-protection-windproof-cap-with-scarf',
                'category_slug' => 'gadget-electronics',
                'regular_price' => 999,
                'sale_price' => 690,
                'stock_quantity' => 70,
                'is_featured' => true,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-winter-cap.webp',
                'short_description' => 'শীতের তীব্র বাতাস থেকে কান, মাথা ও গলা সুরক্ষিত রাখতে চমৎকার উইন্ডপ্রুফ ক্যাপ ও স্কার্ফ সেট।',
                'description' => '<p>নরম ফারের তৈরি অত্যন্ত আরামদায়ক এবং উষ্ণ উইন্ডপ্রুফ উইন্টার ক্যাপ। নারী ও পুরুষ উভয়ের জন্য মানানসই।</p>',
                'specifications' => [
                    'Material' => 'Wool & Warm Fleece',
                    'Size' => 'Free Size (Stretchable)',
                    'Gender' => 'Unisex',
                ],
            ],
            [
                'name' => '(2 pcs) Girls Hair Band multi colour hair band (🔥 🔴 ক্যাশ অন ডেলিভারি 🔴 🔥)',
                'slug' => 'girls-hair-band-multi-colour-2-pcs',
                'category_slug' => 'gadget-electronics',
                'regular_price' => 699,
                'sale_price' => 499,
                'stock_quantity' => 120,
                'is_featured' => false,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-hair-band.webp',
                'short_description' => 'মেয়েদের জন্য ট্রেন্ডি ও স্টাইলিশ মাল্টিকালার প্রিমিয়াম কোয়ালিটি হেয়ার ব্যান্ড (২ প্যাক)।',
                'description' => '<p>উচ্চমানের ফেব্রিক এবং আরামদায়ক ফিটিং। পার্টি কিংবা ক্যাজুয়াল ব্যবহারের জন্য আদর্শ।</p>',
                'specifications' => [
                    'Pack Size' => '2 Pieces',
                    'Color' => 'Assorted Multi Color',
                ],
            ],
            [
                'name' => '(2 pcs) Hot Sale Car sun visor glasses storage clip',
                'slug' => 'car-sun-visor-glasses-storage-clip-2-pcs',
                'category_slug' => 'gadget-electronics',
                'regular_price' => 790,
                'sale_price' => 499,
                'stock_quantity' => 50,
                'is_featured' => false,
                'is_flash_sale' => true,
                'primary_image' => 'products/sidq-prod-visor-clip.webp',
                'short_description' => 'গাড়ির সান ভাইজরে চশমা, টিকিট বা কার্ড নিরাপদে রাখার জন্য সুবিধাজনক ২ পিসের ক্লিপ।',
                'description' => '<p>গাড়িতে নিরাপদে রোদচশমা এবং জরুরি কার্ড গুছিয়ে রাখার সেরা সমাধান।</p>',
                'specifications' => [
                    'Quantity' => '2 Pieces',
                    'Material' => 'Durable ABS Plastic',
                ],
            ],
            [
                'name' => 'Double-Sided Glass and Cup Cleaner Brush with Suction Base',
                'slug' => 'double-sided-cup-cleaner',
                'category_slug' => 'kitchen-accessories',
                'regular_price' => 550,
                'sale_price' => 390,
                'stock_quantity' => 85,
                'is_featured' => true,
                'is_flash_sale' => false,
                'primary_image' => 'products/sidq-prod-cup-cleaner.jpg',
                'short_description' => 'গ্লাস এবং কাপের ভেতরের ও বাইরের অংশ একসাথে চোখের পলকে পরিষ্কার করার ডাবল সাইডেড ব্রাশ।',
                'description' => '<p>সাকশন বেস দিয়ে বেসিন বা সিঙ্কে আটকে রেখে খুব সহজেই যেকোনো সাইজের গ্লাস চকচকে পরিষ্কার করা যায়।</p>',
                'specifications' => [
                    'Type' => 'Double Sided Rotating Brush',
                    'Mount' => 'Suction Cup Base',
                ],
            ],
            [
                'name' => 'Magic Cleaning Cloth Thickened Microfiber - 5 Pcs Pack',
                'slug' => 'magic-cleaning-cloth',
                'category_slug' => 'bath-cleaning',
                'regular_price' => 600,
                'sale_price' => 450,
                'stock_quantity' => 150,
                'is_featured' => true,
                'is_flash_sale' => false,
                'primary_image' => 'products/sidq-prod-cleaning-cloth.jpg',
                'short_description' => 'কোনো দাগ বা আঁশ ছাড়া আয়না, কাঁচ, গাড়ি ও কিচেন পরিষ্কার করার ৫ পিসের ম্যাজিক ক্লথ।',
                'description' => '<p>উচ্চ শোষণ ক্ষমতা সম্পন্ন প্রিমিয়াম মাইক্রোফাইবার ফ্যাব্রিক। দীর্ঘদিন ব্যবহার উপযোগী।</p>',
                'specifications' => [
                    'Pieces' => '5 Pcs Pack',
                    'Material' => 'Thickened Microfiber',
                ],
            ],
        ];

        foreach ($productsData as $pData) {
            $cat = $categoryModels[$pData['category_slug']] ?? null;
            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id' => $cat?->id,
                    'name' => $pData['name'],
                    'sku' => 'SIDQ-' . strtoupper(Str::random(6)),
                    'regular_price' => $pData['regular_price'],
                    'sale_price' => $pData['sale_price'],
                    'stock_quantity' => $pData['stock_quantity'],
                    'is_featured' => $pData['is_featured'],
                    'is_flash_sale' => $pData['is_flash_sale'],
                    'primary_image' => $pData['primary_image'],
                    'short_description' => $pData['short_description'],
                    'description' => $pData['description'],
                    'specifications' => $pData['specifications'],
                    'is_active' => true,
                ]
            );

            // Add secondary image
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 1],
                ['image_path' => $pData['primary_image']]
            );
        }

        // 5. Banners
        Banner::updateOrCreate(
            ['title' => 'Mega Discount Upto 50% Off'],
            [
                'subtitle' => 'Special Flash Sale Offer',
                'image' => 'banners/sidq-banner-mega-sale.png',
                'target_url' => '#',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // 6. Coupons
        Coupon::updateOrCreate(
            ['code' => 'SIDQ50'],
            [
                'type' => 'fixed',
                'value' => 50.00,
                'min_spend' => 500.00,
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'SIDQ10'],
            [
                'type' => 'percent',
                'value' => 10.00,
                'min_spend' => 1000.00,
                'is_active' => true,
            ]
        );

        // 7. Seed a sample order for demonstration in admin
        $firstProduct = Product::first();
        if ($firstProduct) {
            $order = Order::updateOrCreate(
                ['order_number' => 'SIDQ-2026-00101'],
                [
                    'user_id' => $customer->id,
                    'customer_name' => 'Tariqul Islam',
                    'customer_phone' => '01719876543',
                    'shipping_address' => 'House 42, Road 9, Dhanmondi, Dhaka',
                    'delivery_zone' => 'inside_dhaka',
                    'shipping_charge' => 70.00,
                    'subtotal' => $firstProduct->current_price,
                    'discount_amount' => 0.00,
                    'grand_total' => $firstProduct->current_price + 70.00,
                    'payment_method' => 'cod',
                    'payment_status' => 'pending',
                    'order_status' => 'pending',
                    'customer_note' => 'Please deliver in the evening.',
                ]
            );

            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_id' => $firstProduct->id],
                [
                    'product_name' => $firstProduct->name,
                    'product_image' => $firstProduct->primary_image,
                    'unit_price' => $firstProduct->current_price,
                    'quantity' => 1,
                    'total_price' => $firstProduct->current_price,
                ]
            );
        }
    }
}
