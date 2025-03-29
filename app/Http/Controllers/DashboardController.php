<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // If somehow still not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get total income and expense
        $totalIncome = Transaction::where('user_id', $user->user_id)
            ->where('type', 'income')
            ->sum('amount');
            
        $totalExpense = Transaction::where('user_id', $user->user_id)
            ->where('type', 'expense')
            ->sum('amount');
            
        $balance = $totalIncome - $totalExpense;

        // Get today's transactions
        $todayTransactions = Transaction::where('user_id', $user->user_id)
            ->whereDate('transaction_date', Carbon::today())
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Get data for chart (last 6 months)
        $chartIncomeData = [];
        $chartExpenseData = [];
        $chartLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            // Get monthly income
            $monthlyIncome = Transaction::where('user_id', $user->user_id)
                ->where('type', 'income')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
                
            // Get monthly expense
            $monthlyExpense = Transaction::where('user_id', $user->user_id)
                ->where('type', 'expense')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
                
            $chartIncomeData[] = floatval($monthlyIncome);
            $chartExpenseData[] = floatval($monthlyExpense);
            $chartLabels[] = $month->format('M Y');
        }

        return view('html.dashboard', compact(
            'balance',
            'totalIncome',
            'totalExpense',
            'todayTransactions',
            'chartIncomeData',
            'chartExpenseData',
            'chartLabels'
        ));
    }

    public function getChartData($timeFrame)
    {
        $query = Transaction::where('user_id', Auth::id());
        
        // Set the date range based on timeFrame
        switch ($timeFrame) {
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $groupBy = 'date';
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $groupBy = 'month';
                break;
            case 'monthly':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $groupBy = 'date';
                break;
        }

        // Get income data
        $incomeData = $query->clone()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Get expense data
        $expenseData = $query->clone()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Generate date range
        $dates = [];
        $income = [];
        $expense = [];
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dates[] = $currentDate->format('M d'); // Format for display
            $income[] = $incomeData[$dateKey] ?? 0;
            $expense[] = $expenseData[$dateKey] ?? 0;
            
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $dates,
            'income' => $income,
            'expense' => $expense
        ]);
    }
} 