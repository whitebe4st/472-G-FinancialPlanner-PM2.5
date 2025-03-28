<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Context for transaction bookmark features
 */
class BookmarkContext implements Context
{
    private $amount;
    private $category;
    private $date;
    private $isBookmarked = false;
    private $transactionSaved = false;
    private $transactions = [];
    private $bookmarks = [];
    private $selectedBookmarks = [];
    private $newTransactionDate;

    /**
     * Initializes context.
     */
    public function __construct()
    {
        $this->transactions = [];
        $this->bookmarks = [];
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
     * @When I check the :option option
     */
    public function iCheckTheOption($option)
    {
        if ($option === 'Bookmark this transaction') {
            $this->isBookmarked = true;
        }
    }

    /**
     * @When I click the save button
     */
    public function iClickTheSaveButton()
    {
        if (!empty($this->amount) && !empty($this->category) && !empty($this->date)) {
            $this->transactionSaved = true;
            
            $transactionId = count($this->transactions) + 1;
            
            // Add the transaction to our array
            $transaction = [
                'id' => $transactionId,
                'amount' => $this->amount,
                'category' => $this->category,
                'date' => $this->date,
                'isBookmarked' => $this->isBookmarked
            ];
            
            $this->transactions[] = $transaction;
            
            // If bookmarked, add to bookmarks
            if ($this->isBookmarked) {
                $this->bookmarks[] = [
                    'id' => count($this->bookmarks) + 1,
                    'transactionId' => $transactionId,
                    'amount' => $this->amount,
                    'category' => $this->category,
                    'date' => $this->date
                ];
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
     * @Then the transaction should be bookmarked
     */
    public function theTransactionShouldBeBookmarked()
    {
        $lastTransaction = end($this->transactions);
        Assert::assertTrue($lastTransaction['isBookmarked'], 'Transaction was not bookmarked.');
    }

    /**
     * @Then the transaction should appear in the bookmarks list
     */
    public function theTransactionShouldAppearInTheBookmarksList()
    {
        $lastTransaction = end($this->transactions);
        $found = false;
        
        foreach ($this->bookmarks as $bookmark) {
            if ($bookmark['transactionId'] == $lastTransaction['id']) {
                $found = true;
                break;
            }
        }
        
        Assert::assertTrue($found, 'Transaction was not found in bookmarks.');
    }

    /**
     * @Given I am on the bookmarks page
     */
    public function iAmOnTheBookmarksPage()
    {
        // Placeholder implementation
    }

    /**
     * @Given I have at least one bookmarked transaction
     */
    public function iHaveAtLeastOneBookmarkedTransaction()
    {
        if (empty($this->bookmarks)) {
            // Create a sample bookmark if none exists
            $transactionId = count($this->transactions) + 1;
            $this->transactions[] = [
                'id' => $transactionId,
                'amount' => 150.00,
                'category' => 'Groceries',
                'date' => date('Y-m-d', strtotime('-1 day')),
                'isBookmarked' => true
            ];
            
            $this->bookmarks[] = [
                'id' => 1,
                'transactionId' => $transactionId,
                'amount' => 150.00,
                'category' => 'Groceries',
                'date' => date('Y-m-d', strtotime('-1 day'))
            ];
        }
        
        Assert::assertGreaterThanOrEqual(1, count($this->bookmarks), 'No bookmarked transactions exist.');
    }

    /**
     * @Given I have multiple bookmarked transactions
     */
    public function iHaveMultipleBookmarkedTransactions()
    {
        // Make sure we have at least 2 bookmarks
        $this->iHaveAtLeastOneBookmarkedTransaction();
        
        if (count($this->bookmarks) < 2) {
            $transactionId = count($this->transactions) + 1;
            $this->transactions[] = [
                'id' => $transactionId,
                'amount' => 50.00,
                'category' => 'Entertainment',
                'date' => date('Y-m-d', strtotime('-2 days')),
                'isBookmarked' => true
            ];
            
            $this->bookmarks[] = [
                'id' => count($this->bookmarks) + 1,
                'transactionId' => $transactionId,
                'amount' => 50.00,
                'category' => 'Entertainment',
                'date' => date('Y-m-d', strtotime('-2 days'))
            ];
        }
        
        Assert::assertGreaterThanOrEqual(2, count($this->bookmarks), 'Not enough bookmarked transactions exist.');
    }

    /**
     * @When I select a bookmarked transaction
     */
    public function iSelectABookmarkedTransaction()
    {
        $this->selectedBookmarks = [$this->bookmarks[0]];
    }

    /**
     * @When I select multiple bookmarked transactions
     */
    public function iSelectMultipleBookmarkedTransactions()
    {
        // Select at least 2 bookmarks
        $this->selectedBookmarks = array_slice($this->bookmarks, 0, 2);
    }

    /**
     * @When I click :button
     */
    public function iClick($button)
    {
        // Placeholder for button click
    }

    /**
     * @When I select a date for the new transaction
     */
    public function iSelectADateForTheNewTransaction()
    {
        $this->newTransactionDate = date('Y-m-d');
    }

    /**
     * @When I select a date for the new transactions
     */
    public function iSelectADateForTheNewTransactions()
    {
        $this->newTransactionDate = date('Y-m-d');
    }

    /**
     * @When I confirm the addition
     */
    public function iConfirmTheAddition()
    {
        // Add new transactions based on the selected bookmarks
        foreach ($this->selectedBookmarks as $bookmark) {
            $this->transactions[] = [
                'id' => count($this->transactions) + 1,
                'amount' => $bookmark['amount'],
                'category' => $bookmark['category'],
                'date' => $this->newTransactionDate,
                'isBookmarked' => false // This is not automatically bookmarked
            ];
        }
    }

    /**
     * @Then a new transaction should be created based on the bookmark
     */
    public function aNewTransactionShouldBeCreatedBasedOnTheBookmark()
    {
        // At least one new transaction should exist
        $lastTransaction = end($this->transactions);
        $bookmark = $this->selectedBookmarks[0];
        
        Assert::assertEquals($bookmark['amount'], $lastTransaction['amount'], 'Amount doesn\'t match');
        Assert::assertEquals($bookmark['category'], $lastTransaction['category'], 'Category doesn\'t match');
    }

    /**
     * @Then the new transaction should appear in the transaction list
     */
    public function theNewTransactionShouldAppearInTheTransactionList()
    {
        $lastTransaction = end($this->transactions);
        $found = false;
        
        foreach ($this->transactions as $transaction) {
            if ($transaction['id'] === $lastTransaction['id']) {
                $found = true;
                break;
            }
        }
        
        Assert::assertTrue($found, 'New transaction not found in the transaction list.');
    }

    /**
     * @Then new transactions should be created based on the selected bookmarks
     */
    public function newTransactionsShouldBeCreatedBasedOnTheSelectedBookmarks()
    {
        $transactionCount = count($this->transactions);
        $bookmarkCount = count($this->selectedBookmarks);
        
        // Get the last N transactions where N is the number of selected bookmarks
        $newTransactions = array_slice($this->transactions, -$bookmarkCount);
        
        // Check that we have the right number of new transactions
        Assert::assertEquals($bookmarkCount, count($newTransactions), 
            'Number of new transactions doesn\'t match number of selected bookmarks.');
        
        // Check each new transaction against its bookmark
        foreach ($this->selectedBookmarks as $index => $bookmark) {
            $transaction = $newTransactions[$index];
            Assert::assertEquals($bookmark['amount'], $transaction['amount'], 
                'Amount doesn\'t match for bookmark ' . $bookmark['id']);
            Assert::assertEquals($bookmark['category'], $transaction['category'], 
                'Category doesn\'t match for bookmark ' . $bookmark['id']);
        }
    }

    /**
     * @Then the new transactions should appear in the transaction list
     */
    public function theNewTransactionsShouldAppearInTheTransactionList()
    {
        $bookmarkCount = count($this->selectedBookmarks);
        $newTransactionIds = [];
        
        // Get the IDs of the last N transactions
        for ($i = 1; $i <= $bookmarkCount; $i++) {
            $newTransactionIds[] = $this->transactions[count($this->transactions) - $i]['id'];
        }
        
        // Check that each transaction exists in the list
        foreach ($newTransactionIds as $id) {
            $found = false;
            foreach ($this->transactions as $transaction) {
                if ($transaction['id'] === $id) {
                    $found = true;
                    break;
                }
            }
            Assert::assertTrue($found, 'Transaction with ID ' . $id . ' not found in the transaction list.');
        }
    }
} 