<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::where('user_id', $request->user()->id)
            ->with(['customer', 'professional', 'service']);

        if ($request->date) {
            $query->whereDate('appointment_date', $request->date);
        } elseif ($request->today) {
            $query->whereDate('appointment_date', today());
        }

        if ($request->date_from && $request->date_to) {
            $query->whereDate('appointment_date', '>=', $request->date_from)
                  ->whereDate('appointment_date', '<=', $request->date_to);
        }

        if ($request->professional_id) {
            $query->where('professional_id', $request->professional_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return $query->orderBy('starts_at')->paginate(50);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'professional_id' => 'required|exists:professionals,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'starts_at' => 'required|date_format:H:i',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        
        $startsAt = Carbon::createFromFormat('H:i', $validated['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

        $professionalAmount = round($service->price * $service->professional_percentage / 100, 2);
        $salonAmount = $service->price - $professionalAmount;

        $appointment = Appointment::create(array_merge($validated, [
            'user_id' => $request->user()->id,
            'ends_at' => $endsAt->format('H:i'),
            'service_price' => $service->price,
            'professional_percentage' => $service->professional_percentage,
            'professional_amount' => $professionalAmount,
            'salon_amount' => $salonAmount,
            'status' => 'scheduled',
        ]));

        return response()->json($appointment->load(['customer', 'professional', 'service']), 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return $appointment->load(['customer', 'professional', 'service']);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'professional_id' => 'sometimes|exists:professionals,id',
            'service_id' => 'sometimes|exists:services,id',
            'appointment_date' => 'sometimes|date',
            'starts_at' => 'sometimes|date_format:H:i',
            'status' => 'sometimes|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
        ]);

        if (isset($validated['service_id'])) {
            $service = Service::findOrFail($validated['service_id']);
            $validated['service_price'] = $service->price;
            $validated['professional_percentage'] = $service->professional_percentage;
            $validated['professional_amount'] = round($service->price * $service->professional_percentage / 100, 2);
            $validated['salon_amount'] = $service->price - $validated['professional_amount'];
        }

        $appointment->update($validated);

        return response()->json($appointment->load(['customer', 'professional', 'service']));
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return response()->json(null, 204);
    }
}