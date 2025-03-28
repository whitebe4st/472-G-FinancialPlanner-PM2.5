<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Context for financial dashboard features
 */
class SummaryContext implements Context
{
    private $isLoggedIn = false;
    private $currentPage = '';
    private $selectedMonth = '';
    private $transactions = [];
    private $summaryData = [];
    private $chartData = [];
    
    /**
     * Initializes context.
     */
    public function __construct()
    {
        // Create sample transaction data for testing
        $this->transactions = [
            // January
            [
                'id' => 1,
                'amount' => 2000.00,
                'category' => 'Salary',
                'type' => 'income',
                'date' => '2023-01-15'
            ],
            [
                'id' => 2,
                'amount' => 500.00,
                'category' => 'Rent',
                'type' => 'expense',
                'date' => '2023-01-01'
            ],
            [
                'id' => 3,
                'amount' => 200.00,
                'category' => 'Groceries',
                'type' => 'expense',
                'date' => '2023-01-10'
            ],
            [
                'id' => 4,
                'amount' => 100.00,
                'category' => 'Entertainment',
                'type' => 'expense',
                'date' => '2023-01-20'
            ],
            
            // February
            [
                'id' => 5,
                'amount' => 2000.00,
                'category' => 'Salary',
                'type' => 'income',
                'date' => '2023-02-15'
            ],
            [
                'id' => 6,
                'amount' => 500.00,
                'category' => 'Rent',
                'type' => 'expense',
                'date' => '2023-02-01'
            ],
            [
                'id' => 7,
                'amount' => 150.00,
                'category' => 'Groceries',
                'type' => 'expense',
                'date' => '2023-02-10'
            ],
            [
                'id' => 8,
                'amount' => 80.00,
                'category' => 'Entertainment',
                'type' => 'expense',
                'date' => '2023-02-20'
            ],
        ];
        
        // Set current month as default
        $this->selectedMonth = date('Y-m');
        
        // Calculate initial summary data
        $this->calculateSummary($this->selectedMonth);
    }
    
    /**
     * Calculate financial summary for a given month
     */
    private function calculateSummary($yearMonth)
    {
        $totalIncome = 0;
        $totalExpenses = 0;
        $expenseCategories = [];
        
        foreach ($this->transactions as $transaction) {
            $transactionYearMonth = substr($transaction['date'], 0, 7);
            
            if ($transactionYearMonth === $yearMonth) {
                if ($transaction['type'] === 'income') {
                    $totalIncome += $transaction['amount'];
                } else {
                    $totalExpenses += $transaction['amount'];
                    
                    // Track expenses by category for pie chart
                    $category = $transaction['category'];
                    if (!isset($expenseCategories[$category])) {
                        $expenseCategories[$category] = 0;
                    }
                    $expenseCategories[$category] += $transaction['amount'];
                }
            }
        }
        
        $remainingBalance = $totalIncome - $totalExpenses;
        
        $this->summaryData = [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'remainingBalance' => $remainingBalance
        ];
        
        $this->chartData = [
            'barChart' => [
                'income' => $totalIncome,
                'expenses' => $totalExpenses
            ],
            'pieChart' => $expenseCategories
        ];
    }

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->isLoggedIn = true;
    }

    /**
     * @When I visit the dashboard page
     */
    public function iVisitTheDashboardPage()
    {
        if (!$this->isLoggedIn) {
            throw new \Exception('User is not logged in');
        }
        
        $this->currentPage = 'dashboard';
    }
    
    /**
     * @Given I am on the dashboard page
     */
    public function iAmOnTheDashboardPage()
    {
        $this->iAmLoggedIn();
        $this->iVisitTheDashboardPage();
    }

    /**
     * @Then I should see the current month's total income
     */
    public function iShouldSeeTheCurrentMonthsTotalIncome()
    {
        Assert::assertNotNull($this->summaryData['totalIncome'], 'Total income is not displayed');
    }

    /**
     * @Then I should see the current month's total expenses
     */
    public function iShouldSeeTheCurrentMonthsTotalExpenses()
    {
        Assert::assertNotNull($this->summaryData['totalExpenses'], 'Total expenses are not displayed');
    }

    /**
     * @Then I should see the current month's remaining balance
     */
    public function iShouldSeeTheCurrentMonthsRemainingBalance()
    {
        Assert::assertNotNull($this->summaryData['remainingBalance'], 'Remaining balance is not displayed');
    }

    /**
     * @Then I should see a chart comparing income and expenses
     */
    public function iShouldSeeAChartComparingIncomeAndExpenses()
    {
        Assert::assertArrayHasKey('barChart', $this->chartData, 'Chart data is not available');
        Assert::assertArrayHasKey('income', $this->chartData['barChart'], 'Income data for chart is missing');
        Assert::assertArrayHasKey('expenses', $this->chartData['barChart'], 'Expenses data for chart is missing');
    }

    /**
     * @When I select a different month from the month selector
     */
    public function iSelectADifferentMonthFromTheMonthSelector()
    {
        // Change to February 2023
        $this->selectedMonth = '2023-02';
        $this->calculateSummary($this->selectedMonth);
    }

    /**
     * @Then the financial summary should update with data for the selected month
     */
    public function theFinancialSummaryShouldUpdateWithDataForTheSelectedMonth()
    {
        // Verify the data is for February
        $febIncome = 0;
        $febExpenses = 0;
        
        foreach ($this->transactions as $transaction) {
            if (substr($transaction['date'], 0, 7) === '2023-02') {
                if ($transaction['type'] === 'income') {
                    $febIncome += $transaction['amount'];
                } else {
                    $febExpenses += $transaction['amount'];
                }
            }
        }
        
        Assert::assertEquals($febIncome, $this->summaryData['totalIncome'], 
            'Income for the selected month does not match expected value');
        Assert::assertEquals($febExpenses, $this->summaryData['totalExpenses'], 
            'Expenses for the selected month do not match expected values');
        Assert::assertEquals($febIncome - $febExpenses, $this->summaryData['remainingBalance'], 
            'Remaining balance for the selected month does not match expected value');
    }

    /**
     * @Then the charts should update with data for the selected month
     */
    public function theChartsShouldUpdateWithDataForTheSelectedMonth()
    {
        // Check that bar chart data is updated
        Assert::assertEquals($this->summaryData['totalIncome'], $this->chartData['barChart']['income'],
            'Bar chart income data does not match the summary data');
        Assert::assertEquals($this->summaryData['totalExpenses'], $this->chartData['barChart']['expenses'],
            'Bar chart expenses data does not match the summary data');
        
        // Check that pie chart has reasonable data
        $totalPieValue = array_sum($this->chartData['pieChart']);
        Assert::assertEquals($this->summaryData['totalExpenses'], $totalPieValue,
            'Pie chart total does not match the total expenses');
    }
}
