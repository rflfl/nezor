<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('user_id', $request->user()->id)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"))
            ->orderBy('name');

        if ($request->active !== null) {
            $query->where('active', $request->boolean('active'));
        }

        return $query->paginate(20);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        return $customer->load(['appointments', 'dailyServiceEntries']);
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        $customer->delete();
        return response()->json(null, 204);
    }
}