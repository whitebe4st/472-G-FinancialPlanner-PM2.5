<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Context for transaction input features
 */
class InsertContext implements Context
{
    private $amount;
    private $category;
    private $date;
    private $errorMessage;
    private $transactionSaved = false;
    private $transactions = [];

    /**
     * Initializes context.
     */
    public function __construct()
    {
        $this->transactions = [];
    }

    /**
     * @Given I am on the transaction input page
     */
    public function iAmOnTheTransactionInputPage()
    {
        // Placeholder implementation
    }

    /**
     * @When I enter a valid amount
     */
    public function iEnterAValidAmount()
    {
        $this->amount = 100.00;
    }

    /**
     * @When I select a category
     */
    public function iSelectACategory()
    {
        $this->category = 'Food';
    }

    /**
     * @When I select a date
     */
    public function iSelectADate()
    {
        $this->date = date('Y-m-d');
    }

    /**
     * @When I click the save button
     */
    public function iClickTheSaveButton()
    {
        // Check if all required fields are filled
        if (!empty($this->amount) && !empty($this->category) && !empty($this->date)) {
            $this->transactionSaved = true;
            
            // Add the transaction to our array
            $this->transactions[] = [
                'id' => count($this->transactions) + 1,
                'amount' => $this->amount,
                'category' => $this->category,
                'date' => $this->date
            ];
        } else {
            $this->transactionSaved = false;
            if (empty($this->amount)) {
                $this->errorMessage = 'Amount is required';
            } elseif (empty($this->category)) {
                $this->errorMessage = 'Category is required';
            } elseif (empty($this->date)) {
                $this->errorMessage = 'Date is required';
            }
        }
    }

    /**
     * @Then the transaction should be saved
     */
    public function theTransactionShouldBeSaved()
    {
        Assert::assertTrue($this->transactionSaved, 'Transaction was not saved.');
    }

    /**
     * @Then the transaction should appear in the transaction list
     */
    public function theTransactionShouldAppearInTheTransactionList()
    {
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['amount'] == $this->amount && 
                $transaction['category'] == $this->category && 
                $transaction['date'] == $this->date) {
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, 'Transaction not found in the list.');
    }

    /**
     * @When I leave the amount field empty
     */
    public function iLeaveTheAmountFieldEmpty()
    {
        $this->amount = null;
    }

    /**
     * @Then I should see an error message indicating amount is required
     */
    public function iShouldSeeAnErrorMessageIndicatingAmountIsRequired()
    {
        Assert::assertEquals('Amount is required', $this->errorMessage);
    }

    /**
     * @When I do not select a category
     */
    public function iDoNotSelectACategory()
    {
        $this->category = null;
    }

    /**
     * @Then I should see an error message indicating category is required
     */
    public function iShouldSeeAnErrorMessageIndicatingCategoryIsRequired()
    {
        Assert::assertEquals('Category is required', $this->errorMessage);
    }

    /**
     * @When I do not select a date
     */
    public function iDoNotSelectADate()
    {
        $this->date = null;
    }

    /**
     * @Then I should see an error message indicating date is required
     */
    public function iShouldSeeAnErrorMessageIndicatingDateIsRequired()
    {
        Assert::assertEquals('Date is required', $this->errorMessage);
    }
} 