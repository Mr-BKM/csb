<?php

namespace App\Http\Controllers;

use App\Models\Orderm;
use Illuminate\Http\Request;
use Validator;

class ModifyorderController extends Controller
{
    protected $orderm;

    /**
     * Initialize the controller with the Orderm model instance.
     */
    public function __construct()
    {
        $this->orderm = new Orderm();
    }

    /**
     * Fetch and display orders that have already been 'Added' (confirmed with PO details).
     * This allows users to review and modify Purchase Order information.
     */
    public function showData(Request $request)
    {
        // Retrieve all orders where the state is 'Added', sorted by their order ID
        $orderms = Orderm::where('po_state', 'Added')
                         ->orderBy('order_id', 'asc')
                         ->get();

        // Return the modifyorder view with the retrieved order list
        return view('pages.modifyorder', compact('orderms'));
    }

    /**
     * Update the Purchase Order (PO) details for a specific order item.
     * This is used to correct or update supplier and PO reference info.
     */
    public function updateData(Request $request, $id)
    {
        // Define validation rules for the PO modification
        $rules = [
            'po_date'   => 'required|date',           // Must be a valid date
            'po_number' => 'required|string|max:255', // PO reference string
            'sup_id'    => 'required|string|max:15',  // Supplier unique ID
            'sup_name'  => 'required|string|max:255', // Supplier name
        ];

        // Custom error messages to provide clear feedback to the user
        $messages = [
            'po_date.required'   => 'PO Date is required.',
            'po_number.required' => 'PO Number is required.',
            'sup_id.required'    => 'Supplier ID is required.',
            'sup_name.required'  => 'Supplier Name is required.',
        ];

        // Execute the validation
        $validator = Validator::make($request->all(), $rules, $messages);

        // Find the specific record in the Orderm table by its ID
        $orderm = $this->orderm->find($id);

        // Check if the order record actually exists before attempting an update
        if (!$orderm) {
            return redirect('orderm')->with('error', 'Order Item not found.');
        }

        // Apply only the validated data to the record
        $orderm->update($validator->validated());

        // Redirect back with a success notification
        return redirect()->back()->with('success', 'PO Item updated successfully.');
    }
}
