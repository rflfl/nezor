<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('user_id', $request->user()->id)
            ->when($request->category, fn($q) => $q->where('category', $request->category))
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
            'category' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'price' => 'required|numeric|min:0',
            'professional_percentage' => 'required|numeric|min:0|max:100',
            'salon_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $professionalPercentage = (float) $validated['professional_percentage'];
        $salonPercentage = (float) $validated['salon_percentage'];

        if (round($professionalPercentage + $salonPercentage, 2) !== 100.00) {
            return response()->json([
                'message' => 'A soma dos percentuais deve ser 100%',
                'errors' => [
                    'salon_percentage' => ['A soma dos percentuais deve ser 100%']
                ]
            ], 422);
        }

        $service = Service::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($service, 201);
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return $service;
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:255',
            'duration_minutes' => 'sometimes|integer|min:5|max:480',
            'price' => 'sometimes|numeric|min:0',
            'professional_percentage' => 'sometimes|numeric|min:0|max:100',
            'salon_percentage' => 'sometimes|numeric|min:0|max:100',
            'active' => 'sometimes|boolean',
        ]);

        if (isset($validated['professional_percentage']) || isset($validated['salon_percentage'])) {
            $professionalPercentage = (float) ($validated['professional_percentage'] ?? $service->professional_percentage);
            $salonPercentage = (float) ($validated['salon_percentage'] ?? $service->salon_percentage);

            if (round($professionalPercentage + $salonPercentage, 2) !== 100.00) {
                return response()->json([
                    'message' => 'A soma dos percentuais deve ser 100%',
                    'errors' => [
                        'salon_percentage' => ['A soma dos percentuais deve ser 100%']
                    ]
                ], 422);
            }
        }

        $service->update($validated);

        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return response()->json(null, 204);
    }

    public function categories(Request $request)
    {
        $categories = Service::where('user_id', $request->user()->id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return response()->json($categories);
    }
}