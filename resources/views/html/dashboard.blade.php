@extends('layout/layout')

@section('title')
    Dashboard
@endsection

@section('content')
<div class="content-wrapper" style="padding: 2rem;">
    <!-- Summary Cards -->
    <div class="summary-cards" style="display: flex; gap: 2rem; margin-bottom: 2rem; margin-top: 1rem;">
        <div class="card" style="flex: 1; padding: 1.5rem; background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #666; font-size: 1.2rem; margin-bottom: 0.5rem;">BALANCE</h2>
            <p style="font-size: 2rem; font-weight: 600;">{{ number_format($balance, 2) }}</p>
        </div>
        
        <div class="card" style="flex: 1; padding: 1.5rem; background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #666; font-size: 1.2rem; margin-bottom: 0.5rem;">TOTAL INCOME</h2>
            <p style="font-size: 2rem; font-weight: 600; color: #28a745;">{{ number_format($totalIncome, 2) }}</p>
        </div>
        
        <div class="card" style="flex: 1; padding: 1.5rem; background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #666; font-size: 1.2rem; margin-bottom: 0.5rem;">TOTAL EXPENSE</h2>
            <p style="font-size: 2rem; font-weight: 600; color: #dc3545;">{{ number_format($totalExpense, 2) }}</p>
        </div>
    </div>

    <!-- Flex container for Transaction History and Today's Transactions -->
    <div style="display: flex; gap: 2rem;">
        <!-- Transaction History Chart -->
        <div class="chart-section" style="flex: 2; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); height: 400px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="font-size: 1.2rem;">Transaction History</h2>
                <select id="timeFilter" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="transactionChart"></canvas>
            </div>
        </div>

        <!-- Transaction Today -->
        <div class="today-transactions" style="flex: 1; background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-height: 400px; overflow-y: auto;">
            <h2 style="font-size: 1.2rem; margin-bottom: 1rem;">Transaction Today</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 0.75rem; text-align: left; font-size: 0.9rem;">Transaction</th>
                        <th style="padding: 0.75rem; text-align: right; font-size: 0.9rem;">Amount</th>
                        <th style="padding: 0.75rem; text-align: right; font-size: 0.9rem;">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayTransactions as $transaction)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.75rem; font-size: 0.9rem;">{{ $transaction->description }}</td>
                        <td style="padding: 0.75rem; text-align: right; font-size: 0.9rem;">{{ number_format($transaction->amount, 2) }}</td>
                        <td style="padding: 0.75rem; text-align: right; font-size: 0.9rem; color: {{ $transaction->type === 'income' ? '#28a745' : '#dc3545' }}">
                            {{ ucfirst($transaction->type) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', function() {
    const chartElement = document.getElementById('transactionChart');
    let chart = null;
    
    // Initial chart data
    const chartData = {
        monthly: {
            labels: {{ Js::from($chartLabels) }},
            incomeData: {{ Js::from($chartIncomeData) }},
            expenseData: {{ Js::from($chartExpenseData) }}
        }
    };

    function createChart(data) {
        if (chart) {
            chart.destroy();
        }

        const ctx = chartElement.getContext('2d');
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Income',
                    data: data.incomeData,
                    borderColor: '#28a745',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Expense',
                    data: data.expenseData,
                    borderColor: '#dc3545',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Create initial chart
    if (chartElement) {
        createChart(chartData.monthly);

        // Add event listener for filter changes
        document.getElementById('timeFilter').addEventListener('change', function(e) {
            const timeFrame = e.target.value;
            
            // Fetch data for the selected time frame
            fetch(`/api/transactions/chart-data/${timeFrame}`)
                .then(response => response.json())
                .then(data => {
                    chartData[timeFrame] = data;
                    createChart(data);
                })
                .catch(error => console.error('Error fetching chart data:', error));
        });
    }
});
</script>
@endpush
@endsection

