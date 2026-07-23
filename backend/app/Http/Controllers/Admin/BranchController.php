<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('sort_order')->orderBy('id')->paginate(50);
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'phones'    => 'nullable|array',
            'phones.*'  => 'nullable|string|max:50',
        ]);

        $branch = Branch::create([
            'name'       => $r->name,
            'address'    => $r->address,
            'phones'     => array_values(array_filter($r->input('phones', []))),
            'sort_order' => (Branch::max('sort_order') ?? 0) + 1,
            'is_active'  => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Branch added successfully', 'branch' => $branch]);
    }

    public function show(Branch $branch)
    {
        return response()->json($branch);
    }

    public function update(Request $r, Branch $branch)
    {
        if ($r->has('status') && !$r->has('name')) {
            $branch->update(['is_active' => (bool) $r->input('status')]);
            return response()->json(['success' => true, 'message' => 'Status updated']);
        }

        $r->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'phones'    => 'nullable|array',
            'phones.*'  => 'nullable|string|max:50',
        ]);

        $branch->name    = $r->name;
        $branch->address = $r->address;
        $branch->phones  = array_values(array_filter($r->input('phones', [])));
        $branch->save();

        return response()->json(['success' => true, 'message' => 'Branch updated successfully', 'branch' => $branch]);
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return response()->json(['success' => true]);
    }
}
