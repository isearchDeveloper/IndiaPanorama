<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBlogItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeBlogItemController extends Controller
{
    public function index()
    {
        $items = HomeBlogItem::ordered()->get()->map(function ($item) {
            $item->image_url = $item->image ? storage_link($item->image) : null;
            return $item;
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title'     => 'nullable|string|max:255',
            'image'     => 'required|string|exists:media,path',
            'image_alt' => 'nullable|string|max:255',
            'link'      => 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $path = $request->input('image');

        $item = HomeBlogItem::create([
            'title'      => $request->input('title'),
            'image'      => $path,
            'image_alt'  => $request->input('image_alt'),
            'link'       => $request->input('link'),
            'sort_order' => (HomeBlogItem::max('sort_order') ?? 0) + 1,
        ]);

        return response()->json(['success' => true, 'message' => 'Blog item added.', 'data' => $item]);
    }

    public function show(HomeBlogItem $homeBlogItem)
    {
        $homeBlogItem->image_url = $homeBlogItem->image ? storage_link($homeBlogItem->image) : null;

        return response()->json(['success' => true, 'data' => $homeBlogItem]);
    }

    public function update(Request $request, HomeBlogItem $homeBlogItem)
    {
        $v = Validator::make($request->all(), [
            'title'     => 'nullable|string|max:255',
            'image'     => 'nullable|string|exists:media,path',
            'image_alt' => 'nullable|string|max:255',
            'link'      => 'nullable|string|max:500',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $data = $request->only(['title', 'image_alt', 'link']);

        if ($request->has('image')) {
            $data['image'] = $request->input('image');
        }

        $homeBlogItem->update($data);

        return response()->json(['success' => true, 'message' => 'Blog item updated.', 'data' => $homeBlogItem->fresh()]);
    }

    public function destroy(HomeBlogItem $homeBlogItem)
    {
        // Note: the blog item's image file is intentionally NOT deleted here —
        // it now lives in the shared Media Library and may still be referenced
        // elsewhere. Delete it from the Media Library directly
        // (admin.media.destroy) if it's truly no longer needed anywhere.
        $homeBlogItem->delete();

        return response()->json(['success' => true, 'message' => 'Blog item deleted.']);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order'              => 'required|array|min:1',
            'order.*.id'         => 'required|integer|exists:home_blog_items,id',
            'order.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->order as $item) {
            HomeBlogItem::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated.']);
    }
}
