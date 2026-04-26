<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        return CashRegister::where('user_id', $request->user()->id)
            ->orderBy('open_date', 'desc')
            ->orderBy('open_time', 'desc')
            ->paginate(30);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $openCashRegister = CashRegister::where('user_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if ($openCashRegister) {
            return response()->json([
                'message' => 'Já existe um caixa aberto',
                'errors' => ['opening_amount' => ['Já existe um caixa aberto']]
            ], 422);
        }

        $cashRegister = CashRegister::create([
            'user_id' => $request->user()->id,
            'opened_by' => $request->user()->id,
            'open_date' => Carbon::today(),
            'open_time' => Carbon::now()->format('H:i:s'),
            'opening_amount' => $validated['opening_amount'],
            'status' => 'open',
        ]);

        return response()->json($cashRegister, 201);
    }

    public function show(CashRegister $cashRegister)
    {
        $this->authorize('view', $cashRegister);
        return $cashRegister->load(['dailyServiceEntries']);
    }

    public function update(Request $request, CashRegister $cashRegister)
    {
        $this->authorize('update', $cashRegister);

        if ($cashRegister->status === 'closed') {
            return response()->json([
                'message' => 'Caixa já está fechado',
                'errors' => ['status' => ['Caixa já está fechado']]
            ], 422);
        }

        if ($request->has('closing_amount')) {
            $validated = $request->validate([
                'closing_amount' => 'required|numeric|min:0',
                'closing_note' => 'nullable|string',
            ]);

            $difference = $validated['closing_amount'] - $cashRegister->expected_amount;

            $cashRegister->update([
                'counted_amount' => $validated['closing_amount'],
                'difference_amount' => $difference,
                'closing_note' => $validated['closing_note'] ?? null,
                'closed_by' => $request->user()->id,
                'status' => 'closed',
            ]);
        }

        return response()->json($cashRegister);
    }

    public function destroy(CashRegister $cashRegister)
    {
        $this->authorize('delete', $cashRegister);

        if ($cashRegister->status === 'open') {
            $cashRegister->dailyServiceEntries()->delete();
        }

        $cashRegister->delete();

        return response()->json(null, 204);
    }

    public function open(Request $request)
    {
        $cashRegister = CashRegister::where('user_id', $request->user()->id)
            ->where('status', 'open')
            ->first();
        return $cashRegister 
            ? response()->json($cashRegister)
            : response()->json(null);
    }
}