<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Department;
use Illuminate\Support\Str;

class TeamController extends Controller {

    public function index(Request $r){
        if ($r->exists('id')) {
            return response()->json(['teams'=> Team::with('department')->where('id',$r->id)->first()]);
        } else {
            $department = Department::all();
            $teams = Team::with('department')->orderBy('id','desc')
            ->paginate(25); 
            // echo '<pre>';
            // print_r($teams->toArray());die;
            return view('admin.cms.team', compact('teams','department')); 
        }
    }

    public function store(Request $r){
        $validated = $r->validate([
            'name' => 'required',
            'dep_id'       => 'required',
            'description'       => 'required',
            'about'       => 'nullable',
            'profile_image'=> 'required|string|exists:media,path'
        ]);

        $path = $r->input('profile_image');
        $team = Team::create([
            'name'       => $validated['name'],
            'dep_id'  => $validated['dep_id'],
            'profile_image'=> $path,
            'description' => $validated['description'],
            'about' => $validated['about']
        ]);

        if ($r->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Team created successfully',
                'team' => $team
            ]);
        }
        // Fallback for non-AJAX
        return redirect()
        ->route('admin.teams.index')
        ->with('success', 'Team updated successfully');
    }


    public function edit(Team $team){
        if (request()->ajax()) {
            $team->load('department');
            $data = $team->toArray();
            return response()->json($data);
        }
    }


    public function update(Request $r, Team $team){

        if (!$r->exists('status')) {
            $r->validate([
                'name' => 'required',
                'dep_id'       => 'required',
                'description'       => 'required',
                'about'       => 'nullable',
                'profile_image'=> 'nullable|string|exists:media,path'
            ]);

            $imageChanged = $r->has('profile_image') && $r->input('profile_image') !== $team->profile_image;

            $path = $team->profile_image ?? '';
            if ($imageChanged) {
                $path = $r->input('profile_image');

                // Save path in model
                $team->profile_image = $path;
            }



            $team->update([
                'name'       => $r->name,
                'dep_id'  => $r->dep_id,
                'profile_image'=> $path,
                'description' => $r->description,
                'about' => $r->about,
            ]);
        } else {
            $team->is_active = $r->status;
            $team->save();
        }

        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Team updated successfully',
                'data'    => $team
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
        ->route('admin.teams.index')
        ->with('success', 'Member updated successfully');
    }


    public function destroy(Team $team){
        $team->delete();
        return response()->json(['success'=>true]);
    }

}
