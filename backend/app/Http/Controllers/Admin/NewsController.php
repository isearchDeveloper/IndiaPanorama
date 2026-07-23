<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\news;
use App\Models\NewsFaq;
use App\Models\NewsMetaData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $r)
    {
        if($r->exists('id') && $r->exists('faqs')){
            $news = news::with('faqs')->where('id', $r->id)->first();
            if ($r->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'news'    => $news
                ]);
            }
        }else{
         $news = news::latest()->paginate(25);
         return view('admin.news.index', compact('news'));
        }
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'               => 'required|in:news,newsletter',
            'title'              => 'required|string|max:255',
            'slug'               => 'required|unique:news,slug',
            'news_date'          => 'nullable|date',
            'news_thumbnail'     => 'required|string|exists:media,path',
            'news_thumbnail_alt' => 'nullable|string|max:255',
            'description'        => 'required',
        ]);

        $path = $request->input('news_thumbnail');
        $createdAt = $request->filled('news_date')
    ? \Carbon\Carbon::parse($request->news_date)
    : now();

        $news = News::create([
            'title'           => $request->title,
            'slug'            => Str::slug($request->slug),
            'type'            => $request->type,
            'description'     => $request->description,
            'author_name'     => Auth::user()?->name,
            'primary_img'     => $path,
            'primary_img_alt' => $request->news_thumbnail_alt,
            'created_at'      => $createdAt,
            'updated_at'      => now(),
        ]);

        return redirect()->route('admin.news.index')->with('success', 'News added successfully!');
    }

    public function edit(Request $request, News $news)
    {
        News::findorfail($news->id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        if ($request->exists('status')) {
            $news->update(array('is_active' => $request->status));
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'News status updated successfully',
                ]);
            }
        } elseif($request->exists('meta_setting')){
          if ($news->meta) {
            $news->meta->update([
                'news_id' => $news->id,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'h1_heading' => $request->h1_heading,
                'meta_details' => $request->meta_details,
            ]);  
          }else{
          NewsMetaData::create([
                'news_id' => $news->id,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'h1_heading' => $request->h1_heading,
                'meta_details' => $request->meta_details,
            ]);
          }
           return redirect()->route('admin.news.index')->with('success', 'News meta updated successfully!');
        }else {
            $request->validate([
                'type'               => 'required|in:news,newsletter',
                'title'              => 'required|string|max:255',
                'news_date'          => 'nullable|date',
                'news_thumbnail'     => 'nullable|string|exists:media,path',
                'news_thumbnail_alt' => 'nullable|string|max:255',
                'description'        => 'required',
            ]);

            $imageChanged = $request->has('news_thumbnail') && $request->input('news_thumbnail') !== $news->primary_img;

            if ($imageChanged) {
                $path = $request->input('news_thumbnail');
            } else {
                $path =  $news->primary_img;
            }

            $createdAt = $request->filled('news_date')
    ? \Carbon\Carbon::parse($request->news_date)
    : $news->created_at;

            $data = [
                'title'           => $request->title,
                'description'     => $request->description,
                'type'            => $request->type,
                'author_name'     => Auth::user()?->name,
                'primary_img'     => $path,
                'primary_img_alt' => $request->news_thumbnail_alt,
                'created_at'      => $createdAt,
                'updated_at'      => now(),
            ];
            $news->update($data);
            return redirect()->route('admin.news.index')->with('success', 'News updated successfully!');
        }
    }

    public function showMeta(News $news)
    {
        $news->load('meta');
        return response()->json($news);
    }

    public function updateFaq(Request $r, News $news)
    {
        $news->faqs()->delete();
        if ($r->has('faqs')) {
            foreach ($r->faqs as $obj) {
                NewsFaq::create([
                    'news_id' => $news->id,
                    'question' => $obj['question'],
                    'answer'   => $obj['answer'] ?? null,
                ]);
            }
        }
        $news->faq_title = $r->faq_title;
        $news->save();
        return redirect()->route('admin.news.index')->with('success', 'News faq updated successfully');
    }
    
    
    
    public function destroy(News $news)
    {
        $news->faqs()->delete();
        $news->meta()->delete();
        $news->delete();
        return response()->json(['success' => true]);
    }

}
