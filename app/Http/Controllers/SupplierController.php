<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Handle the storage of a new supplier.
     */
    public function saveData(Request $request)
    {
        // Define validation rules for input fields
        $rules = [
            'sup_id' => 'required|string|max:10|unique:suppliers,sup_id',
            'sup_name' => 'required|string|max:255',
            'sup_address' => 'required|string|max:255',
            'sup_telephone' => 'required|string|max:15',
            'sup_description' => 'nullable|string|max:255',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails and redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save valid data to the database using the Model
        Supplier::create($validator->validated());

        return redirect()->back()->with('success', 'Supplier added successfully!');
    }

    /**
     * Display a list of suppliers with search and auto-generated ID.
     */
    public function showData(Request $request)
    {
        $search = trim($request->search);

        // Fetch suppliers with conditional search filters and pagination
        $suppliers = Supplier::when(!empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sup_id', 'like', '%' . $search . '%')
                    ->orWhere('sup_name', 'like', '%' . $search . '%')
                    ->orWhere('sup_address', 'like', '%' . $search . '%');
            });
        })->paginate(10);

        // Auto-generate the next Supplier ID (e.g., S001, S002)
        $lastSupplier = Supplier::orderBy('sup_id', 'desc')->first();
        if ($lastSupplier) {
            // Extract numeric part, increment it, and pad with zeros
            $lastIdNumber = (int) substr($lastSupplier->sup_id, 1);
            $newSupplierId = 'S' . str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Default ID for the first record
            $newSupplierId = 'S001';
        }

        return view('pages.suplier', compact('suppliers', 'newSupplierId'));
    }

    /**
     * Update an existing supplier's details.
     */
    public function updateData(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sup_name' => 'required|string|max:255',
            'sup_address' => 'required|string|max:255',
            'sup_telephone' => 'required|string|max:15',
            'sup_description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if the supplier exists before updating
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier not found.');
        }

        // Perform the update with validated data
        $supplier->update($validator->validated());

        return redirect('suplier')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Delete a supplier with error handling.
     */
    public function deleteData($id)
    {
        try {
            $supplier = Supplier::find($id);

            // Handle case where ID might not exist
            if (!$supplier) {
                return redirect()->back()->with('error', 'Supplier not found.');
            }

            $supplier->delete();
            return redirect('suplier')->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            // Catch database constraint errors (e.g., if supplier is linked to products)
            return redirect()->back()->with('error', 'Cannot delete! This record might be in use elsewhere.');
        }
    }

       /**
     * Search Supplier via AJAX (useful for Select2 or dynamic dropdowns).
     */
    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');

        $supplier = Supplier::where('sup_name', 'like', "%{$search}%")
            ->orderBy('sup_name')
            ->limit(20)
            ->get();

        $formatted = $supplier->map(
            fn($supplier) => [
                'id' => $supplier->sup_id,
                'text' => $supplier->sup_name,
            ],
        );

        return response()->json($formatted);
    }
}
