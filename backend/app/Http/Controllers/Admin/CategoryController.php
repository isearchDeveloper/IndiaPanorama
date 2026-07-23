<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller {

    public function index(Request $r){
        if ($r->exists('id')) {
            return response()->json(['category'=> Category::where('id',$r->id)->first()]);
        } else {
            $categories = Category::withCount('packages')
            ->orderBy('id','desc')
            ->paginate(25); 
            return view('admin.categories.index', compact('categories')); 
        }
    }

    public function create(){
        return redirect()->route('admin.categories.index');
    }

    public function store(Request $r){
        $validated = $r->validate([
            'name'        => 'required|max:150',
            'description' => 'required|max:500',
            'slug'        => 'required|max:150|unique:categories,slug',
            'title'       => 'required|max:150',
            'sub_title'   => 'nullable|max:150',
            'banner_image'=> 'required|string|exists:media,path'
        ]);

        $path = $r->input('banner_image');
        $category = Category::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'title'       => $validated['title'],
            'sub_title'   => $validated['sub_title'],
            'banner_image'=> $path,
            'description' => $validated['description']
        ]);

        if ($r->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Category created successfully',
                'category' => $category
            ]);
        }
        // Fallback for non-AJAX
        return redirect()
        ->route('admin.categories.index')
        ->with('success', 'Category updated successfully');
    }


    public function edit(Category $category){
        if (request()->ajax()) {
            $data = $category->toArray();
            return response()->json($data);
        }
    }


    public function update(Request $r, Category $category){

        if (!$r->exists('status')) {
            $r->validate([
                'name'        => 'required|max:150',
                'title'       => 'required|max:150',
                'sub_title'   => 'nullable|max:150',
                'description' => 'required|max:500',
                'banner_image'=> 'nullable|string|exists:media,path',
            ]);

            $imageChanged = $r->has('banner_image') && $r->input('banner_image') !== $category->banner_image;

            $path = $category->banner_image ?? '';
            if ($imageChanged) {
                $path = $r->input('banner_image');

                $category->banner_image = $path; // save path in model
            }


            $category->update([
                'name'        => $r->name,
                'title'       => $r->title,
                'sub_title'   => $r->sub_title,
                'banner_image'=> $path,
                'description' => $r->description,
            ]);
        } else {
            $category->is_active = $r->status;
            $category->save();
        }

        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Category updated successfully',
                'data'    => $category
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
        ->route('admin.categories.index')
        ->with('success', 'Category updated successfully');
    }


    public function destroy(Category $category){
        $category->delete();
        return response()->json(['success'=>true]);
    }

    public function slugDuplicateCheck(Request $r){ 
        return response()->json([
            'exists' => Category::where('slug', $r->slug)
            ->where('id', '!=', $r->id)
            ->exists()
        ]);

    }
}
