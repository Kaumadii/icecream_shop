<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // GET /api/customers?search=nimal
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->query('search'), function ($q, $s) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%"));
            })
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json($customers);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function store(Request $request)
    {
        $customer = Customer::create($this->validated($request));

        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request, $customer));

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        $req = $request->isMethod('put') || $request->isMethod('post') ? 'required' : 'sometimes';

        return $request->validate([
            'name'    => "$req|string|max:255",
            'email'   => ['nullable', 'email', Rule::unique('customers', 'email')->ignore($customer?->id)],
            'phone'   => "$req|string|max:20",
            'address' => 'nullable|string|max:255',
        ]);
    }
}
