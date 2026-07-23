<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SitemapService;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = (new SitemapService())->generate();
        $xml  = view('admin.settings.sitemap', compact('urls'));

        return response($xml, 200, [
            'Content-Type'        => 'application/xml',
            'Content-Disposition' => 'attachment; filename="sitemap.xml"',
        ]);
    }
}
