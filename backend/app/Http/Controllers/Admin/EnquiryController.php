<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $category = in_array($request->get('category'), Enquiry::CATEGORIES, true)
            ? $request->get('category')
            : 'holidays';

        $counts = Enquiry::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $enquiriesQuery = Enquiry::inCategory($category)->latest();

        if ($search = $request->get('search')) {
            $enquiriesQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $enquiries = $enquiriesQuery->paginate(20)->withQueryString();

        if ($request->ajax() && $request->boolean('ajax')) {
            return response()->json([
                'html' => view('admin.enquiries._table', compact('enquiries'))->render(),
            ]);
        }

        return view('admin.enquiries.index', compact('enquiries', 'category', 'counts'));
    }

    public function show(Enquiry $enquiry)
    {
        $data = $enquiry->toArray();
        $data['created_at'] = $enquiry->created_at?->format('d M Y, h:i A');

        return response()->json($data);
    }

    public function updateStatus(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Enquiry::STATUSES),
        ]);

        $enquiry->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return response()->json(['status' => true, 'message' => 'Enquiry deleted.']);
    }
}
