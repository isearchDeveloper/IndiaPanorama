<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FestivalSetting;
use Illuminate\Http\Request;

class FestivalSettingController extends Controller
{
    /** Table-format listing page — single row representing the Festivals hub page. */
    public function index()
    {
        $setting = FestivalSetting::current();
        return view('admin.festivals.setting', compact('setting'));
    }

    /** JSON fetch for the Settings / Stats / Why Experience / FAQs / Meta modals. */
    public function data()
    {
        $setting = FestivalSetting::current();
        $setting->load(['faqs', 'highlights', 'whyExperiences']);
        $data = $setting->toArray();
        return response()->json($data);
    }

    public function toggleStatus()
    {
        $setting = FestivalSetting::current();
        $setting->update(['is_active' => !$setting->is_active]);
        return response()->json(['status' => true, 'is_active' => $setting->is_active, 'message' => 'Status updated.']);
    }

    /**
     * Single endpoint for all section saves — dispatches on the `section` field,
     * mirroring CityGuideSettingController::updateSection().
     */
    public function updateSection(Request $request)
    {
        $setting = FestivalSetting::current();

        switch ($request->input('section')) {

            case 'banner_settings':
                $request->validate([
                    'banner_image' => 'nullable|string|exists:media,path',
                ]);

                $imageChanged = $request->has('banner_image') && $request->input('banner_image') !== $setting->banner_image;

                $data = $request->only(['title', 'banner_text', 'banner_image_alt', 'short_description']);

                if ($imageChanged) {
                    $data['banner_image'] = $request->input('banner_image');
                }

                $setting->update($data);
                break;

            case 'meta':
                $setting->update($request->only([
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'h1_heading',
                    'meta_details',
                ]));
                break;

            case 'faqs':
                $setting->update($request->only(['faq_title', 'faq_sub_title']));
                $setting->faqs()->delete();
                foreach ($request->input('faqs', []) as $i => $row) {
                    if (!empty($row['question'])) {
                        $setting->faqs()->create([
                            'question'   => $row['question'],
                            'answer'     => $row['answer'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            case 'why_choose':
                $setting->update($request->only(['why_choose_title', 'why_choose_sub_title']));
                $setting->highlights()->delete();
                foreach ($request->input('highlights', []) as $i => $row) {
                    if (!empty($row['stat'])) {
                        $setting->highlights()->create([
                            'icon'       => $row['icon'] ?? null,
                            'stat'       => $row['stat'],
                            'label'      => $row['label'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            case 'why_experience':
                $setting->update($request->only(['why_experience_title', 'why_experience_sub_title']));
                $setting->whyExperiences()->delete();
                foreach ($request->input('why_experiences', []) as $i => $row) {
                    if (!empty($row['title'])) {
                        $setting->whyExperiences()->create([
                            'title'      => $row['title'],
                            'tagline'    => $row['tagline'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Unknown section.'], 422);
        }

        ActivityLog::log('updated', 'FestivalSetting', 'Updated Festivals landing page settings');

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }
}
