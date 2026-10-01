<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    /**
     * Display the Help & Technical Support Portal.
     */
    public function index()
    {
        $dbStatus = 'Connected';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        $systemInfo = [
            'app_name' => config('app.name', 'SIDQ MART'),
            'engine' => 'SIDQ Commerce Enterprise Edition',
            'developer' => 'SIDQ Technology (সিদিক টেকনোলজি)',
            'lead_developer' => 'Jasim Uddin (Evan)',
            'phone' => '01568706310',
            'whatsapp' => '01568706310',
            'whatsapp_url' => 'https://wa.me/8801568706310?text=' . urlencode('Hello SIDQ Technology Support, I need assistance with my store.'),
            'bio_link' => 'https://bio.link/jasimuddin',
            'facebook_url' => 'https://www.facebook.com/jasimuddinevan',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS,
            'database_status' => $dbStatus,
            'environment' => config('app.env', 'production'),
        ];

        return view('admin.support.index', compact('systemInfo'));
    }
}
