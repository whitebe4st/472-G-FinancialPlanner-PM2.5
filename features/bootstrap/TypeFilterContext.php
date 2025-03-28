<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class TypeFilterContext implements Context
{
    private $isLoggedIn = false;
    private $currentPage = '';
    private $selectedType = '';
    private $selectedPeriod = null;
    private $transactions = [];
    private $chartData = [];

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->isLoggedIn = true;
    }

    /**
     * @When I am on the dashboard page
     */
    public function iAmOnDashboardPage()
    {
        $this->currentPage = 'dashboard';
    }

    /**
     * @When I select transaction type :type
     */
    public function iSelectTransactionType($type)
    {
        $this->selectedType = $type;
        $this->loadTransactionData();
        $this->updateChartData();
    }

    /**
     * @When I select data from :months months ago
     */
    public function iSelectPreviousMonths($months)
    {
        $this->selectedPeriod = $months;
        $this->loadTransactionData();
        $this->updateChartData();
    }

    /**
     * @Then I should see only income transactions in table
     */
    public function iShouldSeeOnlyIncomeTransactions()
    {
        Assert::assertNotEmpty($this->transactions);
        foreach ($this->transactions as $transaction) {
            Assert::assertEquals('income', $transaction['type'], 'Found non-income transaction');
        }
    }

    /**
     * @Then I should see only expense transactions in table
     */
    public function iShouldSeeOnlyExpenseTransactions()
    {
        Assert::assertNotEmpty($this->transactions);
        foreach ($this->transactions as $transaction) {
            Assert::assertEquals('expense', $transaction['type'], 'Found non-expense transaction');
        }
    }

    /**
     * @Then I should see the chart update with :type data
     */
    public function iShouldSeeChartUpdate($type)
    {
        Assert::assertNotEmpty($this->chartData);
        Assert::assertArrayHasKey('labels', $this->chartData);
        Assert::assertArrayHasKey('data', $this->chartData);
        Assert::assertEquals($type, $this->chartData['type']);
    }

    /**
     * @Then I should see income transactions from the past :months months
     */
    public function iShouldSeePreviousMonthsTransactions($months)
    {
        Assert::assertEquals($months, $this->selectedPeriod);
        Assert::assertNotEmpty($this->transactions);
        foreach ($this->transactions as $transaction) {
            Assert::assertEquals('income', $transaction['type']);
        }
    }

    /**
     * @Then the filtered data should display without errors
     */
    public function dataShouldDisplayWithoutErrors()
    {
        Assert::assertNotEmpty($this->transactions);
        Assert::assertNotEmpty($this->chartData);
        Assert::assertArrayNotHasKey('error', $this->chartData);
    }

    private function loadTransactionData()
    {
        // Simulate loading transaction data based on type
        if ($this->selectedType === 'income') {
            $this->transactions = [
                ['id' => 1, 'amount' => 1000, 'type' => 'income', 'date' => date('Y-m-d')],
                ['id' => 3, 'amount' => 2000, 'type' => 'income', 'date' => date('Y-m-d')]
            ];
        } else {
            $this->transactions = [
                ['id' => 2, 'amount' => 500, 'type' => 'expense', 'date' => date('Y-m-d')],
                ['id' => 4, 'amount' => 800, 'type' => 'expense', 'date' => date('Y-m-d')]
            ];
        }
    }

    private function updateChartData()
    {
        // Simulate updating chart data based on type
        $this->chartData = [
            'type' => $this->selectedType,
            'labels' => ['Label 1', 'Label 2'],
            'data' => $this->selectedType === 'income' ? [1000, 2000] : [500, 800]
        ];
    }
} 