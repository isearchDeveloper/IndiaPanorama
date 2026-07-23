<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExperienceSetting;
use Illuminate\Http\Request;

class ExperienceSettingController extends Controller
{
    /** Table-format listing page — single row representing the Experiences hub page. */
    public function index()
    {
        $setting = ExperienceSetting::current();
        return view('admin.experiences.setting', compact('setting'));
    }

    /** JSON fetch for the Settings / Best Time / Why Choose / FAQs / Meta modals. */
    public function data()
    {
        $setting = ExperienceSetting::current();
        $setting->load(['faqs', 'bestTimes', 'whyChooseItems']);
        return response()->json($setting);
    }

    public function toggleStatus()
    {
        $setting = ExperienceSetting::current();
        $setting->update(['is_active' => !$setting->is_active]);
        return response()->json(['status' => true, 'is_active' => $setting->is_active, 'message' => 'Status updated.']);
    }

    /**
     * Single endpoint for all section saves — dispatches on the `section` field,
     * mirroring the retired ThemeSettingController::updateSection().
     */
    public function updateSection(Request $request)
    {
        $setting = ExperienceSetting::current();

        switch ($request->input('section')) {

            case 'banner_settings':
                $request->validate(['banner_image' => 'nullable|string|exists:media,path']);

                $data = $request->only(['title', 'banner_text', 'banner_image_alt', 'short_description']);

                if ($request->has('banner_image') && $request->input('banner_image') !== $setting->banner_image) {
                    $data['banner_image'] = $request->input('banner_image');
                }

                $setting->update($data);
                break;

            case 'best_time':
                $setting->update(['best_time_title' => $request->best_time_title]);
                $setting->bestTimes()->delete();
                foreach ($request->input('best_times', []) as $i => $row) {
                    if (!empty($row['label'])) {
                        $setting->bestTimes()->create([
                            'label'      => $row['label'],
                            'text'       => $row['text'] ?? null,
                            'sort_order' => $i,
                        ]);
                    }
                }
                break;

            case 'why_choose':
                $setting->update([
                    'why_choose_title'       => $request->why_choose_title,
                    'why_choose_description' => $request->why_choose_description,
                ]);
                $setting->whyChooseItems()->delete();
                foreach ($request->input('why_choose_items', []) as $i => $row) {
                    if (!empty($row['label'])) {
                        $setting->whyChooseItems()->create([
                            'label'      => $row['label'],
                            'sort_order' => $i,
                        ]);
                    }
                }
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

            default:
                return response()->json(['status' => false, 'message' => 'Unknown section.'], 422);
        }

        ActivityLog::log('updated', 'ExperienceSetting', 'Updated Experiences landing page settings');

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }
}
