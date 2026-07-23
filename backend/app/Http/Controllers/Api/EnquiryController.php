<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EnquiryMail;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/enquiries
    //
    // category        — optional, one of Enquiry::CATEGORIES. When omitted, it's
    //                    derived from `enquiry_type` (e.g. a module's own "Enquire"
    //                    button can pass category directly; the site-wide Enquiries
    //                    dropdown relies on the enquiry_type -> category mapping).
    // enquiry_type    — required, e.g. "Tour Booking", "Car & Bus Booking".
    // country, budget, no_of_persons, travel_date, duration, arrival_city,
    // departure_city  — all optional; only richer forms (e.g. "Plan Trip",
    //                    "Customized Tour Booking") send these. Simpler forms
    //                    (General Enquiries, etc.) just omit them. Each caller's
    //                    own form decides which of these it treats as required —
    //                    this endpoint stays permissive since it's shared by every
    //                    enquiry type on the site.
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category'        => 'nullable|string|in:' . implode(',', Enquiry::CATEGORIES),
            'enquiry_type'    => 'required|string|max:255',
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'required|string|max:30',
            'message'         => 'nullable|string',
            'source_url'      => 'nullable|string|max:2048',
            'country'         => 'nullable|string|max:255',
            'budget'          => 'nullable|string|max:255',
            'no_of_persons'   => 'nullable|integer|min:1',
            'travel_date'     => 'nullable|date',
            'duration'        => 'nullable|string|max:255',
            'arrival_city'    => 'nullable|string|max:255',
            'departure_city'  => 'nullable|string|max:255',
        ]);

        $enquiry = Enquiry::create([
            'category'        => $validated['category'] ?? Enquiry::categoryForType($validated['enquiry_type']),
            'enquiry_type'    => $validated['enquiry_type'],
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'phone'           => $validated['phone'],
            'message'         => $validated['message'] ?? null,
            'source_url'      => $validated['source_url'] ?? $request->headers->get('referer'),
            'ip_address'      => $request->ip(),
            'country'         => $validated['country'] ?? null,
            'budget'          => $validated['budget'] ?? null,
            'no_of_persons'   => $validated['no_of_persons'] ?? null,
            'travel_date'     => $validated['travel_date'] ?? null,
            'duration'        => $validated['duration'] ?? null,
            'arrival_city'    => $validated['arrival_city'] ?? null,
            'departure_city'  => $validated['departure_city'] ?? null,
        ]);

        $this->sendEnquiryEmails($enquiry);

        return response()->json([
            'status'  => 'success',
            'message' => 'Thank you — we\'ve received your enquiry and will get back to you shortly.',
        ]);
    }

    /**
     * Admin gets notified of the new enquiry; the customer gets a confirmation
     * copy. Each is sent independently — a failure on one must never block the
     * other, and neither may fail the enquiry submission itself.
     */
    private function sendEnquiryEmails(Enquiry $enquiry): void
    {
        $data = [
            'name'           => $enquiry->name,
            'email'          => $enquiry->email,
            'phone'          => $enquiry->phone,
            'country'        => $enquiry->country,
            'message'        => $enquiry->message,
            'category'       => $enquiry->category,
            'enquiry_type'   => $enquiry->enquiry_type,
            'budget'         => $enquiry->budget,
            'no_of_persons'  => $enquiry->no_of_persons,
            'travel_date'    => $enquiry->travel_date?->format('d M Y'),
            'duration'       => $enquiry->duration,
            'arrival_city'   => $enquiry->arrival_city,
            'departure_city' => $enquiry->departure_city,
            'current_url'    => $enquiry->source_url,
            'ip'             => $enquiry->ip_address,
        ];

        try {
            Mail::to(config('mail.admin_email'))
                ->send(new EnquiryMail($data, $enquiry->enquiry_type . ' — New Enquiry', 'admin'));
        } catch (\Throwable $e) {
            Log::error('[Enquiry] Failed to send admin notification: ' . $e->getMessage());
        }

        try {
            Mail::to($enquiry->email)
                ->send(new EnquiryMail($data, 'Thank you for your enquiry — Indian Panorama', 'customer'));
        } catch (\Throwable $e) {
            Log::error('[Enquiry] Failed to send customer confirmation: ' . $e->getMessage());
        }
    }
}
