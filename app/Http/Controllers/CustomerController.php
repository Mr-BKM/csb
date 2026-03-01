<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search and auto-generated ID.
     */
    public function showData(Request $request)
    {
        $search = trim($request->search);

        // Fetch customers with search filter and pagination
        $customers = Customer::when(!empty($search), function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cus_id', 'like', '%' . $search . '%')
                    ->orWhere('cus_name', 'like', '%' . $search . '%')
                    ->orWhere('cus_address', 'like', '%' . $search . '%')
                    ->orWhere('cus_telephone', 'like', '%' . $search . '%')
                    ->orWhere('cus_description', 'like', '%' . $search . '%');
            });
        })->paginate(10);

        // Auto-generate the next Customer ID (Format: C001, C002)
        $lastCustomer = Customer::orderBy('cus_id', 'desc')->first();

        if ($lastCustomer) {
            // Extract numeric part, increment, and pad with leading zeros
            $lastIdNumber = (int) substr($lastCustomer->cus_id, 1);
            $newCustomerId = 'C' . str_pad($lastIdNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Initial ID if no records exist
            $newCustomerId = 'C001';
        }

        return view('pages.customer', compact('customers', 'newCustomerId'));
    }

    /**
     * Store a newly created customer in the database.
     */
    public function saveData(Request $request)
    {
        $rules = [
            'cus_id' => 'required|string|max:10|unique:customers,cus_id',
            'cus_name' => 'required|string|max:255',
            'cus_address' => 'required|string|max:255',
            'cus_telephone' => 'required|string|max:15',
            'cus_description' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create record using validated data
        Customer::create($validator->validated());

        return redirect()->back()->with('success', 'Customer added successfully!');
    }

    /**
     * Update the specified customer in the database.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            'cus_name' => 'required|string|max:255',
            'cus_address' => 'required|string|max:255',
            'cus_telephone' => 'required|string|max:15',
            'cus_description' => 'nullable|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = Customer::find($id);

        if (!$customer) {
            return redirect('customer')->with('error', 'Customer not found.');
        }

        // Update record
        $customer->update($validator->validated());

        return redirect('customer')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from the database.
     */
    public function deleteData($id)
    {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return redirect()->back()->with('error', 'Customer not found.');
            }

            $customer->delete();
            return redirect('customer')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            // Handle foreign key constraints or database errors
            return redirect()->back()->with('error', 'Cannot delete! This record might be in use.');
        }
    }

    /**
     * Search customers via AJAX (useful for Select2 or dynamic dropdowns).
     */
    public function ajaxSearch(Request $request)
    {
        $search = $request->get('q');

        $customers = Customer::where('cus_name', 'like', "%{$search}%")
            ->orderBy('cus_name')
            ->limit(20)
            ->get();

        $formatted = $customers->map(
            fn($customer) => [
                'id' => $customer->cus_id,
                'text' => $customer->cus_name,
            ],
        );

        return response()->json($formatted);
    }
}
