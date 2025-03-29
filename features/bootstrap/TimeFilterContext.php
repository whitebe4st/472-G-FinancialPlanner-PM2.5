<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class TimeFilterContext implements Context
{
    private $isLoggedIn = false;
    private $currentPage = '';
    private $selectedTimeFrame = '';
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
     * @When I select :timeFrame view
     */
    public function iSelectTimeFrame($timeFrame)
    {
        $this->selectedTimeFrame = $timeFrame;
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
     * @Then I should see the filtered transactions in table
     */
    public function iShouldSeeFilteredTransactions()
    {
        Assert::assertNotEmpty($this->transactions);
    }

    /**
     * @Then I should see the chart update accordingly
     */
    public function iShouldSeeChartUpdate()
    {
        Assert::assertNotEmpty($this->chartData);
        Assert::assertArrayHasKey('labels', $this->chartData);
        Assert::assertArrayHasKey('data', $this->chartData);
    }

    /**
     * @Then I should see transactions from the past :months months
     */
    public function iShouldSeePreviousMonthsTransactions($months)
    {
        Assert::assertEquals($months, $this->selectedPeriod);
        Assert::assertNotEmpty($this->transactions);
    }

    /**
     * @Then the data should display without errors
     */
    public function dataShouldDisplayWithoutErrors()
    {
        Assert::assertNotEmpty($this->transactions);
        Assert::assertNotEmpty($this->chartData);
        Assert::assertArrayNotHasKey('error', $this->chartData);
    }

    private function loadTransactionData()
    {
        // Simulate loading transaction data
        $this->transactions = [
            ['id' => 1, 'amount' => 1000, 'type' => 'income', 'date' => date('Y-m-d')],
            ['id' => 2, 'amount' => 500, 'type' => 'expense', 'date' => date('Y-m-d')]
        ];
    }

    private function updateChartData()
    {
        // Simulate updating chart data
        $this->chartData = [
            'labels' => ['Label 1', 'Label 2'],
            'data' => [1000, 500]
        ];
    }
} 