<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristActivitySetting;
use App\Models\TouristActivitySettingFaq;
use App\Models\TouristActivitySettingHighlight;
use App\Models\TouristActivitySettingPerfectFor;
use App\Models\TouristActivitySettingSeason;
use App\Models\TouristActivitySettingWhyChoose;
use Illuminate\Http\Request;

class TouristActivitySettingController extends Controller
{
    private const RELATIONS = [
        'faqs', 'whyChooses', 'highlights', 'perfectFors', 'seasons',
    ];

    public function index()
    {
        $setting = TouristActivitySetting::current()->load(self::RELATIONS);
        return view('admin.tourist-activities.setting', compact('setting'));
    }

    public function data()
    {
        $setting = TouristActivitySetting::current()->load(self::RELATIONS);
        return response()->json($setting->toArray());
    }

    public function updateSection(Request $r)
    {
        $setting = TouristActivitySetting::current();

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

        if ($r->section === 'why_choose') {
            $setting->update(['why_choose_title' => $r->why_choose_title, 'why_choose_sub_title' => $r->why_choose_sub_title]);
            $setting->whyChooses()->delete();
            foreach ($r->input('why_chooses', []) as $i => $row) {
                if (trim($row['title'] ?? '') === '') continue;
                TouristActivitySettingWhyChoose::create([
                    'setting_id' => $setting->id,
                    'title'      => $row['title'],
                    'tagline'    => $row['tagline'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        if ($r->section === 'meta') {
            $setting->update($r->only(['meta_title', 'meta_description', 'meta_keywords', 'h1_heading', 'meta_details']));
        }

        if ($r->section === 'faqs') {
            $setting->update(['faq_title' => $r->faq_title, 'faq_sub_title' => $r->faq_sub_title]);
            $setting->faqs()->delete();
            foreach ($r->input('faqs', []) as $i => $faq) {
                if (trim($faq['question'] ?? '') === '') continue;
                TouristActivitySettingFaq::create([
                    'setting_id' => $setting->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        }

        if ($r->section === 'stats') {
            $r->validate([
                'stats.*'        => 'nullable|string|max:255',
                'labels.*'       => 'nullable|string|max:255',
                'stats_image'    => 'nullable|string|exists:media,path',
                'stats_image_alt' => 'nullable|string|max:255',
            ]);

            $statsImageChanged = $r->has('stats_image') && $r->input('stats_image') !== $setting->stats_image;

            $imagePath = $setting->stats_image;
            if ($statsImageChanged) {
                $imagePath = $r->input('stats_image');
            }

            $setting->update(['stats_image' => $imagePath, 'stats_image_alt' => $r->stats_image_alt]);

            $setting->highlights()->delete();
            foreach ($r->input('stats', []) as $i => $stat) {
                if (trim($stat) === '') continue;
                TouristActivitySettingHighlight::create([
                    'setting_id' => $setting->id,
                    'stat'       => $stat,
                    'label'      => $r->input("labels.$i"),
                    'sort_order' => $i,
                ]);
            }
        }

        if ($r->section === 'perfect_for') {
            $r->validate([
                'titles.*'            => 'nullable|string|max:255',
                'perfect_for_icons.*' => 'nullable|string|exists:media,path',
            ]);

            $icons = $r->input('perfect_for_icons', []);
            $rows = [];

            foreach ($r->input('titles', []) as $i => $title) {
                if (trim($title) === '') continue;
                $rows[] = ['title' => $title, 'icon' => $icons[$i] ?? null];
            }

            $setting->perfectFors()->delete();

            foreach ($rows as $i => $row) {
                TouristActivitySettingPerfectFor::create($row + ['setting_id' => $setting->id, 'sort_order' => $i]);
            }
        }

        if ($r->section === 'seasons') {
            $setting->update(['seasons_title' => $r->seasons_title]);
            $setting->seasons()->delete();
            foreach ($r->input('seasons', []) as $i => $row) {
                if (trim($row['season_label'] ?? '') === '') continue;
                TouristActivitySettingSeason::create([
                    'setting_id'       => $setting->id,
                    'season_label'     => $row['season_label'],
                    'period_text'      => $row['period_text'] ?? null,
                    'activities_text'  => $row['activities_text'] ?? null,
                    'sort_order'       => $i,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Saved successfully.']);
    }

    public function toggleStatus()
    {
        $setting = TouristActivitySetting::current();
        $setting->update(['is_active' => !$setting->is_active]);
        return response()->json(['status' => true, 'is_active' => $setting->is_active]);
    }
}
