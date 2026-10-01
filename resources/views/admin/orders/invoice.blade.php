<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>চালান (Invoice) #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Rubik', 'Hind Siliguri', sans-serif;
            color: #333;
            margin: 0;
            padding: 30px;
            font-size: 14px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f13124;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 24px;
            font-weight: 700;
            color: #f13124;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: 700;
            font-size: 16px;
            color: #f13124;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 0 auto 15px; text-align: right;">
        <button onclick="window.print()" style="background: #f13124; color: #fff; border: none; padding: 10px 20px; font-weight: 600; border-radius: 4px; cursor: pointer;">
            🖨️ প্রিন্ট করুন (Print Invoice)
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="brand">{{ $siteName }}</div>
                <div style="font-size: 12px; color: #666;">{{ $contactAddress }}</div>
                <div style="font-size: 12px; color: #666;">ফোন: {{ $contactPhone }}</div>
            </div>
            <div class="text-right">
                <h2 style="margin: 0; color: #333;">ইনভয়েস / চালান</h2>
                <div style="font-weight: 600; color: #f13124;">#{{ $order->order_number }}</div>
                <div style="font-size: 12px; color: #666;">তারিখ: {{ $order->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <strong>বিলিং ও ডেলিভারি প্রাপক:</strong>
                <div>{{ $order->customer_name }}</div>
                <div>ফোন: {{ $order->customer_phone }}</div>
                <div>ঠিকানা: {{ $order->shipping_address }}</div>
            </div>
            <div class="text-right">
                <strong>অর্ডার বিবরণ:</strong>
                <div>পেমেন্ট মেথড: {{ strtoupper($order->payment_method) }}</div>
                <div>ডেলিভারি এরিয়া: {{ $order->delivery_zone === 'inside_dhaka' ? 'ঢাকার ভিতরে' : 'ঢাকার বাইরে' }}</div>
                <div>স্ট্যাটাস: {{ ucfirst($order->order_status) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ক্রম</th>
                    <th>পণ্যের বিবরণ</th>
                    <th class="text-right">একক মূল্য</th>
                    <th class="text-right">পরিমাণ</th>
                    <th class="text-right">মোট</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: 500;">{{ $item->product_name }}</div>
                        @if(!empty($item->variant_text))
                        <div style="font-size: 11px; color: #dc2626; margin-top: 2px; font-weight: 600;">{{ $item->variant_text }}</div>
                        @endif
                    </td>
                    <td class="text-right">৳{{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">৳{{ number_format($item->total_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">সাবটোটাল:</td>
                    <td class="text-right">৳{{ number_format($order->subtotal, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">ডেলিভারি চার্জ:</td>
                    <td class="text-right">৳{{ number_format($order->shipping_charge, 0) }}</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td colspan="4" class="text-right" style="color: green;">কুপন ডিসকাউন্ট:</td>
                    <td class="text-right" style="color: green;">-৳{{ number_format($order->discount_amount, 0) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" class="text-right">সর্বমোট প্রদেয় বিল:</td>
                    <td class="text-right">৳{{ number_format($order->grand_total, 0) }}</td>
                </tr>
            </tfoot>
        </table>

        @if($order->customer_note)
        <div style="background: #fdfdfd; border: 1px dashed #ddd; padding: 10px; margin-bottom: 20px; font-size: 12px;">
            <strong>গ্রাহকের নোট:</strong> {{ $order->customer_note }}
        </div>
        @endif

        <div class="footer">
            {{ $siteName }} এর সাথে কেনাকাটা করার জন্য ধন্যবাদ! কোনো সমস্যায় আমাদের কল করুন: {{ $contactPhone }}
            <div style="margin-top: 6px; font-size: 11px; color: #999;">
                System Powered by <strong>SIDQ Technology</strong>
            </div>
        </div>
    </div>
</body>
</html>
