<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyServiceEntry;
use App\Models\CashRegister;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyServiceEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyServiceEntry::where('user_id', $request->user()->id)
            ->with(['customer', 'professional', 'service']);

        if ($request->date) {
            $query->where('service_date', $request->date);
        } elseif ($request->today) {
            $query->where('service_date', Carbon::today());
        }

        if ($request->professional_id) {
            $query->where('professional_id', $request->professional_id);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(50);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'professional_id' => 'required|exists:professionals,id',
            'service_id' => 'required|exists:services,id',
            'service_date' => 'required|date',
            'gross_amount' => 'required|numeric|min:0',
            'professional_percentage' => 'required|numeric|min:0|max:100',
            'payment_status' => 'sometimes|in:pending,paid',
            'payment_method' => 'sometimes|in:cash,pix,card,mixed',
            'notes' => 'nullable|string',
        ]);

        if ($validated['payment_status'] === 'paid') {
            $openCashRegister = CashRegister::where('user_id', $request->user()->id)
                ->where('status', 'open')
                ->first();

            if (!$openCashRegister) {
                return response()->json([
                    'message' => 'Não há caixa aberto. Abra o caixa primeiro.',
                    'errors' => ['cash_register' => ['Não há caixa aberto']]
                ], 422);
            }

            $validated['cash_register_id'] = $openCashRegister->id;
        }

        $professionalAmount = round($validated['gross_amount'] * $validated['professional_percentage'] / 100, 2);
        $salonAmount = $validated['gross_amount'] - $professionalAmount;

        $entry = DailyServiceEntry::create(array_merge($validated, [
            'user_id' => $request->user()->id,
            'professional_amount' => $professionalAmount,
            'salon_amount' => $salonAmount,
        ]));

        if ($entry->payment_status === 'paid' && $entry->cash_register_id) {
            $cashRegister = CashRegister::find($entry->cash_register_id);
            $cashRegister->expected_amount += $salonAmount;
            $cashRegister->save();
        }

        return response()->json($entry->load(['customer', 'professional', 'service']), 201);
    }

    public function show(DailyServiceEntry $dailyServiceEntry)
    {
        $this->authorize('view', $dailyServiceEntry);
        return $dailyServiceEntry->load(['customer', 'professional', 'service', 'cashRegister']);
    }

    public function update(Request $request, DailyServiceEntry $dailyServiceEntry)
    {
        $this->authorize('update', $dailyServiceEntry);

        $wasPaid = $dailyServiceEntry->payment_status === 'paid';
        $willBePaid = $request->get('payment_status') === 'paid';

        $validated = $request->validate([
            'service_date' => 'sometimes|date',
            'gross_amount' => 'sometimes|numeric|min:0',
            'professional_percentage' => 'sometimes|numeric|min:0|max:100',
            'payment_status' => 'sometimes|in:pending,paid',
            'payment_method' => 'sometimes|in:cash,pix,card,mixed',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['gross_amount']) || isset($validated['professional_percentage'])) {
            $grossAmount = $validated['gross_amount'] ?? $dailyServiceEntry->gross_amount;
            $percentage = $validated['professional_percentage'] ?? $dailyServiceEntry->professional_percentage;
            $validated['professional_amount'] = round($grossAmount * $percentage / 100, 2);
            $validated['salon_amount'] = $grossAmount - $validated['professional_amount'];
        }

        if (!$wasPaid && $willBePaid) {
            $openCashRegister = CashRegister::where('user_id', $request->user()->id)
                ->where('status', 'open')
                ->first();

            if (!$openCashRegister) {
                return response()->json([
                    'message' => 'Não há caixa aberto',
                ], 422);
            }

            $validated['cash_register_id'] = $openCashRegister->id;
        }

        $dailyServiceEntry->update($validated);

        return response()->json($dailyServiceEntry->load(['customer', 'professional', 'service']));
    }

    public function destroy(DailyServiceEntry $dailyServiceEntry)
    {
        $this->authorize('delete', $dailyServiceEntry);

        if ($dailyServiceEntry->cash_register_id && $dailyServiceEntry->payment_status === 'paid') {
            $cashRegister = CashRegister::find($dailyServiceEntry->cash_register_id);
            if ($cashRegister && $cashRegister->status === 'open') {
                $cashRegister->expected_amount -= $dailyServiceEntry->salon_amount;
                $cashRegister->save();
            }
        }

        $dailyServiceEntry->delete();

        return response()->json(null, 204);
    }
}