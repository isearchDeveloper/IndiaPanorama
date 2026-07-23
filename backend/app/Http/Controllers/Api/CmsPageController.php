<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CmsPageResource;
use App\Http\Resources\AwardResource;
use App\Http\Resources\TeamResource;
use App\Models\CmsPage;
use App\Models\Award;
use App\Models\Team;
use App\Models\Department;
use App\Models\News;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function pageDetails(Request $r)
    {
        $page_details = CmsPage::with('meta')
            ->where('slug', $r->slug)
            ->firstOrFail();

        $data['details'] = new CmsPageResource($page_details);

        if ($page_details->id == 2) {
            $awards = Award::select('title', 'award_year', 'description', 'banner_image')
                ->where('is_active', 1)->get();
            $data['awards'] = AwardResource::collection($awards);
        }

        if ($page_details->id == 12) {
            $order = ['Directors','Sales','Operation','Customer care','Accounts','Transport','IT','HR','GRE'];
            $data['department'] = Department::select('id', 'name')
                ->orderByRaw("FIELD(name, '" . implode("','", $order) . "')")
                ->get();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Details',
            'data'    => $data,
        ]);
    }

    public function team_list(Request $r)
    {
        $teams = Team::with('department')
            ->where('dep_id', $r->id)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'list',
            'teams'  => TeamResource::collection($teams),
        ]);
    }

    public function newsList(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page  = $request->get('page', 1);

        $news = News::where('type', $request->type)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get()
            ->map(function ($item) {
                $item->primary_img = storage_link($item->primary_img);
                return $item;
            });

        return response()->json([
            'status'  => 'success',
            'message' => 'list',
            'news'    => $news,
        ]);
    }

    public function newsDetails($slug)
    {
        $news = News::with('faqs')
            ->where('is_active', 1)
            ->where('slug', $slug)
            ->first();

        if ($news) {
            $news->primary_img = storage_link($news->primary_img);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'details',
            'data'    => $news,
        ]);
    }
}
