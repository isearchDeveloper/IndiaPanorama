<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('teams')->orderBy('name')->get();
        return view('admin.cms.department', compact('departments'));
    }

    public function store(Request $r)
    {
        $r->validate(['name' => 'required|string|max:255|unique:departments,name']);
        $dept = Department::create(['name' => trim($r->name)]);
        return response()->json(['success' => true, 'department' => $dept]);
    }

    public function edit(Department $department)
    {
        return response()->json($department);
    }

    public function update(Request $r, Department $department)
    {
        $r->validate(['name' => 'required|string|max:255|unique:departments,name,' . $department->id]);
        $department->update(['name' => trim($r->name)]);
        return response()->json(['success' => true, 'department' => $department]);
    }

    public function destroy(Department $department)
    {
        if ($department->teams()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete — ' . $department->teams()->count() . ' team member(s) are assigned to this department.'
            ], 422);
        }
        $department->delete();
        return response()->json(['success' => true]);
    }
}
