<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        $user = Auth::user();
        $chartData = [
            'labels' => [],
            'incomeData' => [],
            'expenseData' => []
        ];
        
        switch($timeFrame) {
            case 'weekly':
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $chartData['labels'][] = $date->format('D');
                    
                    $chartData['incomeData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'income')
                        ->whereDate('transaction_date', $date)
                        ->sum('amount');
                        
                    $chartData['expenseData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'expense')
                        ->whereDate('transaction_date', $date)
                        ->sum('amount');
                }
                break;
                
            case 'yearly':
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $chartData['labels'][] = $date->format('M Y');
                    
                    $chartData['incomeData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'income')
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');
                        
                    $chartData['expenseData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'expense')
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');
                }
                break;
                
            default: // monthly
                // Last 6 months (keep existing logic)
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $chartData['labels'][] = $date->format('M Y');
                    
                    $chartData['incomeData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'income')
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');
                        
                    $chartData['expenseData'][] = Transaction::where('user_id', $user->user_id)
                        ->where('type', 'expense')
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');
                }
        }
        
        return response()->json($chartData);
    }
} 