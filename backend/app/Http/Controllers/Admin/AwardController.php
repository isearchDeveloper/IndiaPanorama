<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Award;
use Illuminate\Support\Str;

class AwardController extends Controller {

    public function index(Request $r){
        if ($r->exists('id')) {
            return response()->json(['awards'=> Award::where('id',$r->id)->first()]);
        } else {
            $awards = Award::orderBy('id','desc')
            ->paginate(25); 
            return view('admin.cms.award', compact('awards')); 
        }
    }

    public function store(Request $r){
        $validated = $r->validate([
            'description' => 'required',
            'title'       => 'required',
            'award_year'       => 'required',
            'banner_image'=> 'required|string|exists:media,path'
        ]);

        $path = $r->input('banner_image');
        $award = Award::create([
            'title'       => $validated['title'],
            'award_year'  => $validated['award_year'],
            'banner_image'=> $path,
            'description' => $validated['description']
        ]);

        if ($r->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Award created successfully',
                'award' => $award
            ]);
        }
        // Fallback for non-AJAX
        return redirect()
        ->route('admin.awards.index')
        ->with('success', 'Award created successfully');
    }


    public function edit(Award $award){
        if (request()->ajax()) {
            $data = $award->toArray();
            return response()->json($data);
        }
    }


    public function update(Request $r, Award $award){

        if (!$r->exists('status')) {
            $r->validate([
                'title'       => 'required',
                'award_year'        => 'required',
                'description' => 'required',
                'banner_image'=> 'nullable|string|exists:media,path'
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $award->banner_image;

            $path = $award->banner_image ?? '';
            if ($imageChanged) {
                $path = $r->input('banner_image');

                // Save path in model
                $award->banner_image = $path;
            }



            $award->update([
                'title'       => $r->title,
                'award_year'  => $r->award_year,
                'banner_image'=> $path,
                'description' => $r->description
            ]);
        } else {
            $award->is_active = $r->status;
            $award->save();
        }

        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Award updated successfully',
                'data'    => $award
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
        ->route('admin.awards.index')
        ->with('success', 'Award updated successfully');
    }


    public function destroy(Award $award){
        $award->delete();
        return response()->json(['success'=>true]);
    }

}
