<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Validator;

class GroupController extends Controller
{
    protected $group;

    public function __construct()
    {
        $this->group = new Group();
    }

    /**
     * Handle data display logic. 
     */
    public function showData(Request $request)
    {
        $search = trim($request->search);

        $groups = Group::when(!empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('grp_id', 'like', '%' . $search . '%')
                  ->orWhere('grp_name', 'like', '%' . $search . '%');
            });
        });

        return redirect()->back()->with('showGroupTable', true);
    }

    /**
     * Validate and store a new group record with Duplicate handling.
     */
    public function saveData(Request $request)
    {
        $rules = [
            'grp_id' => 'required|string|max:10',
            'grp_name' => 'required|string|max:255',
        ];

        $messages = [
            'grp_id.required' => 'Group Id is required.',
            'grp_name.required' => 'Group Name is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showGroupTable', true);
        }

        try {
            $this->group->create($validator->validated());
            return redirect()->back()->with('success', 'Group added successfully!')->with('showGroupTable', true);

        } catch (\Illuminate\Database\QueryException $e) {
            // 1062 is the MySQL error code for Duplicate Entry
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()
                    ->with('error', 'Group name already exists! Please use a unique name.')
                    ->withInput()
                    ->with('showGroupTable', true);
            }
            return redirect()->back()->with('error', 'Database error occurred.');
        }
    }

    /**
     * Fetch group for editing.
     */
    public function editData($id)
    {
        $group = $this->group->find($id);
        
        if (!$group) {
            return redirect()->back()->with('error', 'Group not found.');
        }

        $response['group'] = $group;
        return redirect()->back()->with($response)->with('showGroupTable', true);
    }

    /**
     * Update an existing group record with Duplicate handling.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            'grp_name' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showGroupTable', true);
        }

        $group = $this->group->find($id);

        if (!$group) {
            return redirect()->back()->with('error', 'Group not found.');
        }

        try {
            $group->update($validator->validated());
            return redirect()->back()->with('success', 'Group Updated successfully!')->with('showGroupTable', true);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()
                    ->with('error', 'This Group name is already taken.')
                    ->withInput()
                    ->with('showGroupTable', true);
            }
            return redirect()->back()->with('error', 'Update failed.');
        }
    }

    /**
     * AJAX Search for Select2.
     */
    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');
        $groups = Group::where('grp_name', 'like', "%{$search}%")
            ->orderBy('grp_name')
            ->limit(20)
            ->get();

        $formatted = $groups->map(function ($group) {
            return [
                'id' => $group->grp_id,
                'text' => $group->grp_name,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Delete with foreign key protection.
     */
    public function deleteData($id)
    {
        try {
            $group = $this->group->find($id);
            if (!$group) {
                return redirect()->back()->with('error', 'Group not found.');
            }
            $group->delete();
            return redirect()->back()->with(['success' => 'Group deleted successfully.', 'showGroupTable' => true]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot delete! This record might be in use.');
        }
    }
}