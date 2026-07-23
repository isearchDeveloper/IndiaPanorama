<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    public function index(): JsonResponse
    {
        $partners = Partner::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(capped_limit(request()))
            ->get()
            ->map(fn($p) => [
                'image' => storage_link($p->image),
                'alt'   => $p->alt ?? '',
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Partners list',
            'data'    => $partners,
        ]);
    }
}
