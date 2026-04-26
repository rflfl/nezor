<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\DailyServiceEntry;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Professional;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();
        $endMonth = $today->copy()->endOfMonth();

        $openCashRegister = CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        $dayEntries = DailyServiceEntry::where('user_id', $user->id)
            ->where('service_date', $today)
            ->where('payment_status', 'paid');

        $dayRevenue = $dayEntries->sum('gross_amount');
        $dayCommission = $dayEntries->sum('professional_amount');
        $dayServices = $dayEntries->count();
        $dayCustomers = $dayEntries->distinct('customer_id')->count('customer_id');

        $monthEntries = DailyServiceEntry::where('user_id', $user->id)
            ->whereBetween('service_date', [$startMonth, $endMonth])
            ->where('payment_status', 'paid');

        $monthRevenue = $monthEntries->sum('gross_amount');
        $monthCommission = $monthEntries->sum('professional_amount');
        $monthSalon = $monthEntries->sum('salon_amount');

        $servicesCount = $monthEntries->count();
        $avgTicket = $servicesCount > 0 ? $monthRevenue / $servicesCount : 0;

        $topServices = DailyServiceEntry::where('user_id', $user->id)
            ->whereBetween('service_date', [$startMonth, $endMonth])
            ->selectRaw('service_id, COUNT(*) as count, SUM(gross_amount) as total')
            ->groupBy('service_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($entry) {
                $service = Service::find($entry->service_id);
                return [
                    'name' => $service ? $service->name : 'Serviço',
                    'count' => $entry->count,
                    'total' => $entry->total,
                ];
            });

        $topProfessionals = DailyServiceEntry::where('user_id', $user->id)
            ->whereBetween('service_date', [$startMonth, $endMonth])
            ->selectRaw('professional_id, SUM(professional_amount) as commission')
            ->groupBy('professional_id')
            ->orderByDesc('commission')
            ->limit(5)
            ->get()
            ->map(function ($entry) {
                $professional = Professional::find($entry->professional_id);
                return [
                    'name' => $professional ? $professional->name : 'Profissional',
                    'commission' => $entry->commission,
                ];
            });

        $last7Days = [];
        $last7DaysRevenue = [];
        $last7DaysCommission = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $entries = DailyServiceEntry::where('user_id', $user->id)
                ->where('service_date', $date)
                ->where('payment_status', 'paid');

            $last7Days[] = $date->format('d/m');
            $last7DaysRevenue[] = $entries->sum('gross_amount');
            $last7DaysCommission[] = $entries->sum('professional_amount');
        }

        return response()->json([
            'day' => [
                'revenue' => $dayRevenue,
                'commission' => $dayCommission,
                'services' => $dayServices,
                'customers' => $dayCustomers,
            ],
            'month' => [
                'revenue' => $monthRevenue,
                'commission' => $monthCommission,
                'salon' => $monthSalon,
                'services' => $servicesCount,
                'avg_ticket' => $avgTicket,
            ],
            'open_cash_register' => $openCashRegister ? [
                'id' => $openCashRegister->id,
                'opening_amount' => $openCashRegister->opening_amount,
                'expected_amount' => $openCashRegister->expected_amount,
            ] : null,
            'top_services' => $topServices,
            'top_professionals' => $topProfessionals,
            'last_7_days' => [
                'labels' => $last7Days,
                'revenue' => $last7DaysRevenue,
                'commission' => $last7DaysCommission,
            ],
            'stats' => [
                'total_customers' => Customer::where('user_id', $user->id)->count(),
                'total_professionals' => Professional::where('user_id', $user->id)->where('active', true)->count(),
                'total_services' => Service::where('user_id', $user->id)->where('active', true)->count(),
            ],
        ]);
    }
}