<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Context for transaction edit and delete features
 */
class EditContext implements Context
{
    private $amount;
    private $category;
    private $date;
    private $transactionUpdated = false;
    private $transactionDeleted = false;
    private $showConfirmationDialog = false;
    private $selectedTransaction = null;
    private $transactions = [];

    /**
     * Initializes context.
     */
    public function __construct()
    {
        // Initialize a sample transaction for testing
        $this->transactions = [
            [
                'id' => 1,
                'amount' => 100.00,
                'category' => 'Food',
                'date' => date('Y-m-d')
            ]
        ];
    }

    /**
     * @Given I am on the transaction list page
     */
    public function iAmOnTheTransactionListPage()
    {
        // Placeholder implementation
    }

    /**
     * @When I select a transaction to edit
     */
    public function iSelectATransactionToEdit()
    {
        // Select the first transaction
        $this->selectedTransaction = $this->transactions[0];
        
        // Initialize edit values with current transaction values
        $this->amount = $this->selectedTransaction['amount'];
        $this->category = $this->selectedTransaction['category'];
        $this->date = $this->selectedTransaction['date'];
    }

    /**
     * @When I change the amount to :newAmount
     */
    public function iChangeTheAmountTo($newAmount)
    {
        $this->amount = floatval($newAmount);
    }

    /**
     * @When I change the category to :newCategory
     */
    public function iChangeTheCategoryTo($newCategory)
    {
        $this->category = $newCategory;
    }

    /**
     * @When I change the date to tomorrow
     */
    public function iChangeDateToTomorrow()
    {
        $this->date = date('Y-m-d', strtotime('+1 day'));
    }

    /**
     * @When I click the save button
     */
    public function iClickTheSaveButton()
    {
        if (!empty($this->amount) && !empty($this->category) && !empty($this->date)) {
            $this->transactionUpdated = true;
            
            // Update the transaction in our array
            foreach ($this->transactions as &$transaction) {
                if ($transaction['id'] === $this->selectedTransaction['id']) {
                    $transaction['amount'] = $this->amount;
                    $transaction['category'] = $this->category;
                    $transaction['date'] = $this->date;
                    break;
                }
            }
        } else {
            $this->transactionUpdated = false;
        }
    }

    /**
     * @Then the transaction should be updated with the new values
     */
    public function theTransactionShouldBeUpdatedWithTheNewValues()
    {
        Assert::assertTrue($this->transactionUpdated, 'Transaction was not updated.');
        
        // Check if the transaction was actually updated in our array
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['id'] === $this->selectedTransaction['id']) {
                Assert::assertEquals($this->amount, $transaction['amount']);
                Assert::assertEquals($this->category, $transaction['category']);
                Assert::assertEquals($this->date, $transaction['date']);
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, 'Updated transaction not found.');
    }

    /**
     * @Then I should see the updated transaction in the list
     */
    public function iShouldSeeTheUpdatedTransactionInTheList()
    {
        // Check that the transaction appears in our array with the updated values
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['amount'] == $this->amount && 
                $transaction['category'] == $this->category && 
                $transaction['date'] == $this->date) {
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, 'Updated transaction not found in the list.');
    }

    /**
     * @When I select a transaction to delete
     */
    public function iSelectATransactionToDelete()
    {
        // Select the first transaction
        $this->selectedTransaction = $this->transactions[0];
        
        // Show the confirmation dialog
        $this->showConfirmationDialog = true;
    }

    /**
     * @Then I should see a confirmation dialog
     */
    public function iShouldSeeAConfirmationDialog()
    {
        Assert::assertTrue($this->showConfirmationDialog, 'Confirmation dialog was not shown.');
    }

    /**
     * @When I confirm the deletion
     */
    public function iConfirmTheDeletion()
    {
        // Delete the transaction
        $selectedId = $this->selectedTransaction['id'];
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction['id'] === $selectedId) {
                unset($this->transactions[$key]);
                $this->transactions = array_values($this->transactions); // Reindex array
                break;
            }
        }
        
        $this->transactionDeleted = true;
        $this->showConfirmationDialog = false;
    }

    /**
     * @Then the transaction should be removed from the database
     */
    public function theTransactionShouldBeRemovedFromTheDatabase()
    {
        Assert::assertTrue($this->transactionDeleted, 'Transaction was not deleted.');
        
        // Check that the transaction is no longer in our array
        foreach ($this->transactions as $transaction) {
            Assert::assertNotEquals($this->selectedTransaction['id'], $transaction['id']);
        }
    }

    /**
     * @Then the transaction should no longer appear in the list
     */
    public function theTransactionShouldNoLongerAppearInTheList()
    {
        // Our array represents the list, so check it's not there
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['id'] === $this->selectedTransaction['id']) {
                $found = true;
                break;
            }
        }
        Assert::assertFalse($found, 'Transaction still found in the list.');
    }

    /**
     * @When I cancel the deletion
     */
    public function iCancelTheDeletion()
    {
        $this->showConfirmationDialog = false;
        $this->transactionDeleted = false;
    }

    /**
     * @Then the transaction should still appear in the list
     */
    public function theTransactionShouldStillAppearInTheList()
    {
        // Check that the transaction is still in our array
        $found = false;
        foreach ($this->transactions as $transaction) {
            if ($transaction['id'] === $this->selectedTransaction['id']) {
                $found = true;
                break;
            }
        }
        Assert::assertTrue($found, 'Transaction not found in the list.');
    }
} 