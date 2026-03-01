<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subgroup;
use Validator;

class SubGroupController extends Controller
{
    protected $subgroup;

    /**
     * Constructor to initialize the Subgroup model instance.
     */
    public function __construct()
    {
        $this->subgroup = new Subgroup();
    }

    /**
     * Handle search and display logic for Subgroups.
     */
    public function showData(Request $request)
    {
        $search = trim($request->search);

        // Filter subgroups by ID or Name
        $subgroups = Subgroup::when(!empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("subgrp_id", "like", "%" . $search . "%")
                  ->orWhere("subgrp_name", "like", "%" . $search . "%");
            });
        });

        return redirect()->back()->with('showSubGroupTable', true);
    }

    /**
     * Validate and store a new Subgroup. 
     * Includes handling for duplicate name entries.
     */
    public function saveData(Request $request)
    {
        $rules = [
            "subgrp_id" => "required|string|max:10",
            "subgrp_name" => "required|string|max:255",
        ];

        $messages = [
            "subgrp_id.required" => "Sub Group Id is required.",
            "subgrp_name.required" => "Sub Group Name is required.",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showSubGroupTable', true);
        }

        try {
            $this->subgroup->create($validator->validated());
            return redirect()->back()
                ->with("success", "Sub Group added successfully!")
                ->with('showSubGroupTable', true);

        } catch (\Illuminate\Database\QueryException $e) {
            // Check for duplicate entry error code (1062)
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()
                    ->with('error', 'Sub Group name already exists! Please use a unique name.')
                    ->withInput()
                    ->with('showSubGroupTable', true);
            }
            return redirect()->back()->with('error', 'Database error occurred.');
        }
    }

    /**
     * Fetch a specific subgroup for editing.
     */
    public function editData($id)
    {
        $subgroup = $this->subgroup->find($id);

        if (!$subgroup) {
            return redirect()->back()->with('error', 'Sub Group not found.');
        }

        $response["subgroup"] = $subgroup;
        return redirect()->back()
            ->with($response)
            ->with('showSubGroupTable', true);
    }

    /**
     * Update an existing Subgroup with duplicate check handling.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            "subgrp_name" => "required|string|max:255",
        ];

        $messages = [
            "subgrp_name.required" => "Sub Group Name is required.",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('showSubGroupTable', true);
        }

        $subgroup = $this->subgroup->find($id);

        if (!$subgroup) {
            return redirect()->back()->with("error", "Sub Group not found.");
        }

        try {
            $subgroup->update($validator->validated());
            return redirect()->back()
                ->with("success", "Sub Group Updated successfully!")
                ->with('showSubGroupTable', true);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()
                    ->with('error', 'This Sub Group name is already taken.')
                    ->withInput()
                    ->with('showSubGroupTable', true);
            }
            return redirect()->back()->with('error', 'Update failed.');
        }
    }

    /**
     * Handle AJAX search requests for Select2 dropdowns.
     */
    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');

        $subgroups = Subgroup::where('subgrp_name', 'like', "%{$search}%")
            ->orderBy('subgrp_name')
            ->limit(20)
            ->get();

        $formatted = $subgroups->map(function ($subgroup) {
            return [
                'id' => $subgroup->subgrp_id,
                'text' => $subgroup->subgrp_name
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Delete a subgroup with exception handling for usage constraints.
     */
    public function deleteData($id)
    {
        try {
            $subgroup = $this->subgroup->find($id);
            if (!$subgroup) {
                return redirect()->back()->with('error', 'Sub Group not found.');
            }
            
            $subgroup->delete();
            
            return redirect()->back()->with([
                'success' => 'Sub Group deleted successfully.',
                'showSubGroupTable' => true
            ]);

        } catch (\Exception $e) {
            // Triggered if the record is linked as a foreign key (e.g., in Items table)
            return redirect()->back()->with('error', 'Cannot delete! This record might be in use.');
        }
    }
}