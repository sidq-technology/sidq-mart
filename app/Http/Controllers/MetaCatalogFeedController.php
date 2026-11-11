<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class MetaCatalogFeedController extends Controller
{
    /**
     * Generate dynamic Meta / Facebook Product Catalog XML Feed (RSS 2.0 / Google Merchant format)
     * 
     * Compatible with:
     * - Meta Commerce Manager (Advantage+ Catalog Ads, Dynamic Product Ads, Instagram Shop)
     * - Google Merchant Center
     */
    public function index(): Response
    {
        $xml = Cache::remember('meta_product_catalog_xml', 3600, function () {
            $siteName = htmlspecialchars(Setting::get('site_name', 'SIDQ MART'), ENT_XML1, 'UTF-8');
            $siteUrl = url('/');
            $currency = 'BDT';

            $products = Product::where('is_active', true)
                ->with(['category'])
                ->latest('id')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
            $xml .= '  <channel>' . "\n";
            $xml .= "    <title>{$siteName} Product Feed</title>\n";
            $xml .= "    <link>{$siteUrl}</link>\n";
            $xml .= "    <description>Live Dynamic Product Feed for {$siteName} Catalog</description>\n";

            foreach ($products as $product) {
                $id = $product->id;
                $title = htmlspecialchars($product->name, ENT_XML1, 'UTF-8');
                
                // Clean description
                $rawDesc = $product->short_description ?: ($product->description ?: $product->name);
                $cleanDesc = trim(strip_tags((string) $rawDesc));
                $cleanDesc = htmlspecialchars(mb_substr($cleanDesc, 0, 5000), ENT_XML1, 'UTF-8');

                $link = htmlspecialchars(route('product.detail', $product->slug), ENT_XML1, 'UTF-8');
                $imageLink = htmlspecialchars($product->primary_image_url, ENT_XML1, 'UTF-8');
                $categoryName = htmlspecialchars($product->category?->name ?? 'General', ENT_XML1, 'UTF-8');

                $availability = ($product->stock_quantity > 0) ? 'in stock' : 'out of stock';
                $regularPrice = number_format((float) $product->regular_price, 2, '.', '') . " {$currency}";
                
                $xml .= "    <item>\n";
                $xml .= "      <g:id>{$id}</g:id>\n";
                $xml .= "      <g:title>{$title}</g:title>\n";
                $xml .= "      <g:description>{$cleanDesc}</g:description>\n";
                $xml .= "      <g:link>{$link}</g:link>\n";
                $xml .= "      <g:image_link>{$imageLink}</g:image_link>\n";
                $xml .= "      <g:brand>{$siteName}</g:brand>\n";
                $xml .= "      <g:condition>new</g:condition>\n";
                $xml .= "      <g:availability>{$availability}</g:availability>\n";
                $xml .= "      <g:price>{$regularPrice}</g:price>\n";

                if ($product->sale_price && (float) $product->sale_price < (float) $product->regular_price) {
                    $salePrice = number_format((float) $product->sale_price, 2, '.', '') . " {$currency}";
                    $xml .= "      <g:sale_price>{$salePrice}</g:sale_price>\n";
                }

                $xml .= "      <g:product_type>{$categoryName}</g:product_type>\n";

                if ($product->sku) {
                    $sku = htmlspecialchars($product->sku, ENT_XML1, 'UTF-8');
                    $xml .= "      <g:mpn>{$sku}</g:mpn>\n";
                }

                $label = $product->is_flash_sale ? 'Flash Sale' : ($product->is_featured ? 'Featured' : 'Regular');
                $xml .= "      <g:custom_label_0>{$label}</g:custom_label_0>\n";
                $xml .= "    </item>\n";
            }

            $xml .= '  </channel>' . "\n";
            $xml .= '</rss>';

            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
