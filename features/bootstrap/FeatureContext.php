<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $amount;
    private $category;
    private $date;
    private $errorMessage;
    private $transactionSaved = false;
    private $transactionUpdated = false;
    private $transactionDeleted = false;
    private $showConfirmationDialog = false;
    private $selectedTransaction = null;
    private $transactions = [];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
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
            if ($this->selectedTransaction) {
                // We're updating
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
                // We're creating
                $this->transactionSaved = true;
                
                // Add the transaction to our array
                $this->transactions[] = [
                    'id' => count($this->transactions) + 1,
                    'amount' => $this->amount,
                    'category' => $this->category,
                    'date' => $this->date
                ];
            }
        } else {
            $this->transactionSaved = false;
            $this->transactionUpdated = false;
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
        // Placeholder implementation
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
        $this->theTransactionShouldAppearInTheTransactionList();
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
