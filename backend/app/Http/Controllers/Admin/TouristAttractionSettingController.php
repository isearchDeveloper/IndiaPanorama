<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristAttractionSetting;
use App\Models\TouristAttractionSettingFaq;
use Illuminate\Http\Request;

class TouristAttractionSettingController extends Controller
{
    public function index()
    {
        $setting = TouristAttractionSetting::current()->load('faqs');
        return view('admin.tourist-attractions.setting', compact('setting'));
    }

    public function data()
    {
        $setting = TouristAttractionSetting::current()->load('faqs');
        $data = $setting->toArray();
        return response()->json($data);
    }

    public function updateSection(Request $r)
    {
        $setting = TouristAttractionSetting::current();

        if ($r->section === 'banner_settings') {
            $r->validate([
                'title'             => 'nullable|string|max:255',
                'banner_text'       => 'nullable|string',
                'banner_image'      => 'nullable|string|exists:media,path',
                'banner_image_alt'  => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $setting->banner_image;

            $path = $setting->banner_image;
            if ($imageChanged) {
                $path = $r->input('banner_image');
            }

            $setting->update([
                'title'             => $r->title,
                'banner_text'       => $r->banner_text,
                'banner_image'      => $path,
                'banner_image_alt'  => $r->banner_image_alt,
                'short_description' => $r->short_description,
            ]);
        }

        if ($r->section === 'meta') {
            $setting->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        if ($r->section === 'faqs') {
            $setting->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $setting->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristAttractionSettingFaq::create([
                    'setting_id' => $setting->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus()
    {
        $setting = TouristAttractionSetting::current();
        $setting->update(['is_active' => !$setting->is_active]);
        return response()->json(['status' => true, 'is_active' => $setting->is_active]);
    }
}
