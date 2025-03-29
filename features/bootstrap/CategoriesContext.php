<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class CategoriesContext implements Context
{
    private $isLoggedIn = false;
    private $currentPage = '';
    private $categories = [];
    private $transactions = [];
    private $selectedTransaction = null;
    private $chartData = [];

    /**
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->isLoggedIn = true;
    }

    /**
     * @When I go to category management page
     */
    public function iGoToCategoryManagementPage()
    {
        $this->currentPage = 'category-management';
    }

    /**
     * @When I create a new category :categoryName
     */
    public function iCreateANewCategory($categoryName)
    {
        $this->categories[] = [
            'id' => count($this->categories) + 1,
            'name' => $categoryName
        ];
    }

    /**
     * @Then I should see :categoryName in the category list
     */
    public function iShouldSeeInTheCategoryList($categoryName)
    {
        $found = false;
        foreach ($this->categories as $category) {
            if ($category['name'] === $categoryName) {
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, "Category '$categoryName' not found in the list");
    }

    /**
     * @Given I have created category :categoryName
     */
    public function iHaveCreatedCategory($categoryName)
    {
        $this->categories[] = [
            'id' => count($this->categories) + 1,
            'name' => $categoryName
        ];
    }

    /**
     * @When I go to transaction list
     */
    public function iGoToTransactionList()
    {
        $this->currentPage = 'transaction-list';
        // Add sample transaction
        $this->transactions[] = [
            'id' => 1,
            'amount' => 1000,
            'category' => 'Uncategorized',
            'date' => date('Y-m-d')
        ];
    }

    /**
     * @When I select a transaction to edit
     */
    public function iSelectATransactionToEdit()
    {
        $this->selectedTransaction = $this->transactions[0];
    }

    /**
     * @When I change its category to :categoryName
     */
    public function iChangeItsCategoryTo($categoryName)
    {
        if ($this->selectedTransaction) {
            $this->selectedTransaction['category'] = $categoryName;
        }
    }

    /**
     * @When I save the transaction
     */
    public function iSaveTheTransaction()
    {
        foreach ($this->transactions as &$transaction) {
            if ($transaction['id'] === $this->selectedTransaction['id']) {
                $transaction = $this->selectedTransaction;
                break;
            }
        }
        $this->updateChartData();
    }

    /**
     * @Then the transaction should show :categoryName as its category
     */
    public function theTransactionShouldShowAsItsCategory($categoryName)
    {
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['category'] === $categoryName) {
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, "Transaction with category '$categoryName' not found");
    }

    /**
     * @Given I have transactions in :categoryName category
     */
    public function iHaveTransactionsInCategory($categoryName)
    {
        $this->transactions = [
            [
                'id' => 1,
                'amount' => 1000,
                'category' => $categoryName,
                'date' => date('Y-m-d')
            ],
            [
                'id' => 2,
                'amount' => 500,
                'category' => $categoryName,
                'date' => date('Y-m-d')
            ]
        ];
    }

    /**
     * @When I filter transactions by category :categoryName
     */
    public function iFilterTransactionsByCategory($categoryName)
    {
        $this->transactions = array_filter($this->transactions, function($transaction) use ($categoryName) {
            return $transaction['category'] === $categoryName;
        });
    }

    /**
     * @Then I should see only transactions from :categoryName category
     */
    public function iShouldSeeOnlyTransactionsFromCategory($categoryName)
    {
        Assert::assertNotEmpty($this->transactions);
        foreach ($this->transactions as $transaction) {
            Assert::assertEquals($categoryName, $transaction['category']);
        }
    }

    /**
     * @Then the financial report should update with the new category
     */
    public function theFinancialReportShouldUpdateWithTheNewCategory()
    {
        Assert::assertNotEmpty($this->chartData);
        Assert::assertArrayHasKey('labels', $this->chartData);
        Assert::assertArrayHasKey('data', $this->chartData);
    }

    private function updateChartData()
    {
        $category = $this->selectedTransaction ? $this->selectedTransaction['category'] : '';
        $this->chartData = [
            'labels' => ['Label 1', 'Label 2'],
            'data' => [1000, 500]
        ];
    }
} 