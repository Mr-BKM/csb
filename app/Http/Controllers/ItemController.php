<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Group;
use App\Models\Subgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Validator;

class ItemController extends Controller
{
    protected $item;

    /**
     * Constructor to initialize the Item model.
     */
    public function __construct()
    {
        $this->item = new Item();
    }

    /**
     * Display the main item page.
     */
    public function index()
    {
        return view('pages.item');
    }

    /**
     * Fetch and display filtered data for Items, Groups, and Subgroups.
     * Includes logic for search functionality and auto-generating IDs.
     */
    public function showData(Request $request)
    {
        $search = trim($request->search);
        $tab = $request->input('tab'); // Identify which tab is currently active

        // --- ITEM SEARCH LOGIC ---
        $items = Item::when(!empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("itm_code", "like", "%" . $search . "%")
                    ->orWhere("itm_barcode", "like", "%" . $search . "%")
                    ->orWhere("itm_name", "like", "%" . $search . "%")
                    ->orWhere("itm_group", "like", "%" . $search . "%")
                    ->orWhere("itm_stock", "like", "%" . $search . "%")
                    ->orWhere("itm_page_num", "like", "%" . $search . "%");
            });
        })->paginate(10, ['*'], 'item_page');

        // --- GROUP SEARCH LOGIC ---
        $groupsQuery = Group::when(!empty($search) && $tab === 'groups', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("grp_id", "like", "%" . $search . "%")
                  ->orWhere("grp_name", "like", "%" . $search . "%");
            });
        })->when($tab !== 'groups', function($query) {
            return $query->latest(); 
        });

        $groups = $groupsQuery->get();

        // Handle session flashing to maintain tab state
        if ($tab === 'groups') {
            $request->session()->flash('showGroupTable', true);
        } else {
            $groups = Group::all();
        }

        // Auto-generate next Group ID (e.g., G001, G002)
        $lastGroupID = Group::orderBy('grp_id', 'desc')->first();
        if ($lastGroupID) {
            $lastIdNumber = (int)substr($lastGroupID->grp_id, 1);
            $newIdNumber = $lastIdNumber + 1;
            $newGroupId = 'G' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $newGroupId = 'G001';
        }

        // --- SUBGROUP SEARCH LOGIC ---
        $subgroupsQuery = Subgroup::when(!empty($search) && $tab === 'subgroups', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("subgrp_id", "like", "%" . $search . "%")
                  ->orWhere("subgrp_name", "like", "%" . $search . "%");
            });
        })->when($tab !== 'subgroups', function($query) {
            return $query->latest();
        });

        $subgroups = $subgroupsQuery->get();

        if ($tab === 'subgroups') {
            $request->session()->flash('showSubGroupTable', true);
        } else {
            $subgroups = SubGroup::all();
        }

        // Auto-generate next Subgroup ID (e.g., SUB001)
        $lastsubGroupID = Subgroup::orderBy('subgrp_id', 'desc')->first();
        if ($lastsubGroupID) {
            $lastsubIdNumber = (int)substr($lastsubGroupID->subgrp_id, 3);
            $newsubIdNumber = $lastsubIdNumber + 1;
            $newsubGroupId = 'SUB' . str_pad($newsubIdNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $newsubGroupId = 'SUB001';
        }

        return view("pages.item", compact("items", "newGroupId", "groups", "newsubGroupId", "subgroups"));
    }

    /**
     * Validate and save a new item record.
     */
    public function saveData(Request $request)
    {
        $rules = [
            "itm_code" => "required|string|max:10",
            "itm_barcode" => "nullable|string|max:255",
            "itm_name" => "required|string|max:255",
            "itm_sinhalaname" => "nullable|string|max:255",
            "itm_book_code" => "nullable|string|max:255",
            "itm_page_num" => "nullable|string|max:255",
            "itm_group_id" => "required|string|max:255",
            "itm_group" => "required|string|max:255",
            "itm_subgroup_id" => "nullable|string|max:255",
            "itm_subgroup" => "nullable|string|max:255",
            "itm_unit_of_measure" => "required|string|max:255",
            "itm_book_stock" => "nullable|numeric",
            "itm_stock" => "nullable|numeric",
            "itm_reorder_level" => "nullable|string|max:255",
            "itm_reorder_flag" => "required|string|max:3",
            "itm_description" => "nullable|string|max:255",
        ];

        // Define custom validation messages
        $messages = [
            "itm_code.required" => "Item Code is required.",
            "itm_name.required" => "Item Name is required.",
            "itm_group.required" => "Item Group is required.",
            "itm_unit_of_measure.required" => "Unit of Measure is required.",
            "itm_reorder_flag.required" => "Reorder flag is required.",
            // ... (other messages remain the same)
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->item->create($validator->validated());

        return redirect()->back()->with("success", "Item added successfully!");
    }

    /**
     * Fetch a specific item for editing.
     */
    public function editData($id)
    {
        $response["item"] = $this->item->find($id);
        return view("pages.item")->with($response);
    }

    /**
     * Update an existing item record.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            "itm_barcode" => "nullable|string|max:255",
            "itm_name" => "required|string|max:255",
            "itm_sinhalaname" => "nullable|string|max:255",
            "itm_book_code" => "nullable|string|max:255",
            "itm_page_num" => "nullable|string|max:255",
            "itm_group_id" => "required|string|max:255",
            "itm_group" => "required|string|max:255",
            "itm_subgroup_id" => "nullable|string|max:255",
            "itm_subgroup" => "nullable|string|max:255",
            "itm_unit_of_measure" => "required|string|max:255",
            "itm_book_stock" => "nullable|numeric",
            "itm_stock" => "nullable|numeric",
            "itm_reorder_level" => "nullable|string|max:255",
            "itm_reorder_flag" => "required|string|max:3",
            "itm_description" => "nullable|string|max:255",
        ];

        $validator = Validator::make($request->all(), $rules);

        $item = $this->item->find($id);

        if (!$item) {
            return redirect("item")->with("error", "Item not found.");
        }

        $item->update($validator->validated());

        return redirect("item")->with("success", "Item updated successfully.");
    }

    /**
     * Delete a specific item with error handling.
     */
    public function deleteData($id)
    {
        try {
            $item = Item::find($id);

            if (!$item) {
                return redirect()->back()->with('error', 'Item not found.');
            }

            $item->delete();
            return redirect('item')->with('success', 'Item deleted successfully.');

        } catch (\Exception $e) {
            // Error handling for foreign key constraints or DB issues
            return redirect()->back()->with('error', 'Cannot delete! This record might be in use.');
        }
    }

    /**
     * Handle AJAX requests for item searching (e.g., for Select2 dropdowns).
     */
    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');

        $items = Item::where('itm_name', 'like', "%{$search}%")
            ->orderBy('itm_name')
            ->limit(20)
            ->get();

        $formatted = $items->map(function ($item) {
            return [
                'id' => $item->itm_code,
                'text' => $item->itm_name,
                'barcode' => $item->itm_barcode,
                'sinhalaname' => $item->itm_sinhalaname,
                'bookcode' => $item->itm_book_code,
                'pagenum' => $item->itm_page_num,
                'group' => $item->itm_group,
                'subgroup' => $item->itm_subgroup,
                'unit_of_measure' => $item->itm_unit_of_measure,
                'stockinhand' => $item->itm_stock,
                'status' => $item->itm_status
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Wipe all records from the items table.
     */
    public function deleteAllForeignKey()
    {
        // Disable constraints to allow truncation
        Schema::disableForeignKeyConstraints();

        DB::table('items')->truncate();

        Schema::enableForeignKeyConstraints();

        return redirect()->back()->with('success', 'All items wiped successfully.');
    }

    /**
 * Remove all items safely.
 * Will prevent deletion if foreign key constraints exist.
 */
public function deleteAll()
{
    try {
        // We use delete() instead of truncate() to respect foreign key constraints
        // This will throw an exception if any record is linked to another table
        $deletedCount = Item::query()->delete();

        if ($deletedCount === 0) {
            return redirect()->back()->with('info', 'No items found to delete.');
        }

        return redirect()->back()->with('success', 'All items deleted successfully.');

    } catch (\Exception $e) {
        // This block will catch if a foreign key prevents the deletion
        return redirect()->back()->with('error', 'Cannot wipe all items! Some records are linked to other data.');
    }
}
}