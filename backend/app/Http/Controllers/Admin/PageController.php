<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\HomeController;
use App\Models\Page;
use App\Models\PageFaq;
use App\Models\PageMetaData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class PageController extends Controller{

    public function faqs(Request $r){
        $page = Page::with('faqs')->where('id',$r->id)->first();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'page'    => $page
            ]);
        }
    }

    public function updateFaq(Request $r, Page $page){
        $page->faqs()->delete();
        if ($r->has('faqs')) {
            foreach ($r->faqs as $obj) {
                PageFaq::create([
                    'page_id'  => $page->id,
                    'question' => $obj['question'],
                    'answer'   => $obj['answer'] ?? null,
                ]);
            }
        }
        $page->faq_title = $r->faq_title;
        $page->save();
        $url = getUrl($page->id)['url'];
        return redirect()->route($url)->with('success', 'Faq updated successfully');
    }

    public function showMeta(Page $page){
        $page->load('meta');
        return response()->json($page);
    }

    public function update(Request $r, Page $page){
        if($r->exists('meta_setting')) {
            $r->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
                'meta_body_details'=> 'nullable|string',
            ]);
            $metaData = [
                'meta_title'        => $r->meta_title,
                'meta_description'  => $r->meta_description,
                'meta_keywords'     => $r->meta_keywords,
                'h1_heading'        => $r->h1_heading,
                'meta_details'      => strip_figma_paste_junk($r->meta_details),
                'meta_body_details' => strip_figma_paste_junk($r->meta_body_details),
            ];

            if ($page->meta) {
                $page->meta->update($metaData);
            } else {
                PageMetaData::create(array_merge($metaData, ['page_id' => $page->id]));
            }

            // Home page's SEO meta (page_id 8) is served through the cached
            // /api/v1/home endpoint — without this, a save here wouldn't show
            // up there for up to CACHE_TTL (5 min).
            if ($page->id == 8) {
                HomeController::flushCache();
            }

            if ($r->ajax() || $r->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'SEO / Meta updated successfully.']);
            }

            $url = getUrl($page->id)['url'];
            return redirect()->route($url)->with('success', 'Page updated successfully');
        } else {
            $validatedData = $r->validate([
                'title'        => 'required',
                'description'  => 'required',
                'banner_image' => 'nullable|string|exists:media,path'
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $page->banner_image;

            $path = $page->banner_image ?? '';
            if ($imageChanged) {
                $path = $r->input('banner_image');
            }

            $obj = [
                'title'       => $r->title,
                'description'   => strip_figma_paste_junk($r->description),
                'banner_image'=> $path,
                'banner_image_alt' =>$r->banner_image_alt
            ];

            session()->flash('active_tab', getUrl($page->id)['page']);
            //print_r($obj);die;
            $page->update($obj);
        }
        $url = getUrl($page->id)['url'];
        return redirect()
        ->route($url)
        ->with('success', 'Page updated successfully');
    }



}
