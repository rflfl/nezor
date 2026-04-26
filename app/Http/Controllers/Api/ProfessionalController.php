<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index(Request $request)
    {
        $query = Professional::where('user_id', $request->user()->id)
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
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'default_commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $professional = Professional::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($professional, 201);
    }

    public function show(Professional $professional)
    {
        $this->authorize('view', $professional);
        return $professional->load(['appointments', 'dailyServiceEntries']);
    }

    public function update(Request $request, Professional $professional)
    {
        $this->authorize('update', $professional);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'default_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'active' => 'sometimes|boolean',
        ]);

        $professional->update($validated);

        return response()->json($professional);
    }

    public function destroy(Professional $professional)
    {
        $this->authorize('delete', $professional);
        $professional->delete();
        return response()->json(null, 204);
    }
}