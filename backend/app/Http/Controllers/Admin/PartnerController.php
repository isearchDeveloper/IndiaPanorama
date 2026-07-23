<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    private function licenseForJson(?\App\Models\ImageLicense $license): ?array
    {
        if (!$license) return null;

        return [
            'source_of_image'      => $license->source_of_image,
            'download_date'        => $license->download_date?->format('Y-m-d'),
            'account_id'           => $license->account_id,
            'license_key'          => $license->license_key,
            'license_key_file_url' => $license->license_key_file ? storage_link($license->license_key_file) : null,
        ];
    }

    public function index()
    {
        $partners = Partner::orderBy('sort_order')->orderBy('id')->paginate(50);
        return view('admin.cms.partners', compact('partners'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'image' => 'required|string|exists:media,path',
            'alt'   => 'nullable|string|max:255',
        ]);

        $path = $r->input('image');

        $partner = Partner::create([
            'image'      => $path,
            'alt'        => $r->input('alt', ''),
            'sort_order' => Partner::max('sort_order') + 1,
            'is_active'  => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Partner added successfully', 'partner' => $partner]);
    }

    public function show(Partner $partner)
    {
        $data = $partner->toArray();
        $data['image_license'] = $this->licenseForJson($partner->imageLicense('image'));
        return response()->json($data);
    }

    public function update(Request $r, Partner $partner)
    {
        if ($r->has('status')) {
            $partner->update(['is_active' => (bool) $r->input('status')]);
            return response()->json(['success' => true, 'message' => 'Status updated']);
        }

        $r->validate([
            'image' => 'nullable|string|exists:media,path',
            'alt'   => 'nullable|string|max:255',
        ]);

        if ($r->has('image')) {
            $partner->image = $r->input('image');
        }

        $partner->alt = $r->input('alt', $partner->alt);
        $partner->save();

        return response()->json(['success' => true, 'message' => 'Partner updated successfully', 'partner' => $partner]);
    }

    public function destroy(Partner $partner)
    {
        // Note: the partner's image file is intentionally NOT deleted here — it
        // may live in the shared Media Library and still be referenced
        // elsewhere. Delete it from the Media Library directly
        // (admin.media.destroy) if it's truly no longer needed anywhere.
        $partner->delete();
        return response()->json(['success' => true]);
    }
}
