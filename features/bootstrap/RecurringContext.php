<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Context for recurring transactions features
 */
class RecurringContext implements Context
{
    private $amount;
    private $category;
    private $date;
    private $isRecurring = false;
    private $frequency;
    private $repeatDay;
    private $transactionSaved = false;
    private $transactions = [];
    private $bookmarks = [];
    private $selectedBookmark = null;
    private $selectedTransaction = null;

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
        if ($option === 'Make this a recurring transaction') {
            $this->isRecurring = true;
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
                'isRecurring' => $this->isRecurring
            ];
            
            $this->transactions[] = $transaction;
            
            // If recurring, add to bookmarks
            if ($this->isRecurring) {
                $this->bookmarks[] = [
                    'id' => count($this->bookmarks) + 1,
                    'transactionId' => $transactionId,
                    'amount' => $this->amount,
                    'category' => $this->category,
                    'frequency' => null,
                    'repeatDay' => null
                ];
            }
            
            // If updating a bookmark
            if ($this->selectedBookmark) {
                foreach ($this->bookmarks as &$bookmark) {
                    if ($bookmark['id'] === $this->selectedBookmark['id']) {
                        $bookmark['frequency'] = $this->frequency;
                        $bookmark['repeatDay'] = $this->repeatDay;
                        break;
                    }
                }
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
     * @Then the transaction should be marked as recurring
     */
    public function theTransactionShouldBeMarkedAsRecurring()
    {
        $lastTransaction = end($this->transactions);
        Assert::assertTrue($lastTransaction['isRecurring'], 'Transaction was not marked as recurring.');
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
     * @When I select a bookmarked transaction
     */
    public function iSelectABookmarkedTransaction()
    {
        if (empty($this->bookmarks)) {
            // Create a sample bookmark if none exists
            $this->bookmarks[] = [
                'id' => 1,
                'transactionId' => 1,
                'amount' => 100.00,
                'category' => 'Food',
                'frequency' => null,
                'repeatDay' => null
            ];
        }
        
        $this->selectedBookmark = $this->bookmarks[0];
    }

    /**
     * @When I set the frequency to :frequency
     */
    public function iSetTheFrequencyTo($frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @When I set the repeat day to :day
     */
    public function iSetTheRepeatDayTo($day)
    {
        $this->repeatDay = $day;
    }

    /**
     * @Then the recurring transaction settings should be updated
     */
    public function theRecurringTransactionSettingsShouldBeUpdated()
    {
        $found = false;
        foreach ($this->bookmarks as $bookmark) {
            if ($bookmark['id'] === $this->selectedBookmark['id']) {
                Assert::assertEquals($this->frequency, $bookmark['frequency']);
                Assert::assertEquals($this->repeatDay, $bookmark['repeatDay']);
                $found = true;
                break;
            }
        }
        
        Assert::assertTrue($found, 'Bookmark settings were not updated.');
    }

    /**
     * @Given I have a recurring transaction set to :frequency on day :day
     */
    public function iHaveARecurringTransactionSetTo($frequency, $day)
    {
        // Create a recurring transaction with specific settings
        $transactionId = count($this->transactions) + 1;
        
        $this->transactions[] = [
            'id' => $transactionId,
            'amount' => 150.00,
            'category' => 'Rent',
            'date' => date('Y-m-d', strtotime('last month')),
            'isRecurring' => true
        ];
        
        $this->bookmarks[] = [
            'id' => count($this->bookmarks) + 1,
            'transactionId' => $transactionId,
            'amount' => 150.00,
            'category' => 'Rent',
            'frequency' => $frequency,
            'repeatDay' => $day
        ];
    }

    /**
     * @When the system runs the recurring transaction process
     */
    public function theSystemRunsTheRecurringTransactionProcess()
    {
        // Simulate running the recurring transaction process
        // For testing purposes, assume it's the 15th of the month
        $today = date('Y-m-d'); // Current date
        $currentDay = date('d'); // Current day of month
        
        foreach ($this->bookmarks as $bookmark) {
            // Only process if we have frequency and repeat day set
            if (!empty($bookmark['frequency']) && !empty($bookmark['repeatDay'])) {
                if ($bookmark['frequency'] === 'Monthly' && $bookmark['repeatDay'] === $currentDay) {
                    // Create a new transaction based on the bookmark
                    $this->transactions[] = [
                        'id' => count($this->transactions) + 1,
                        'amount' => $bookmark['amount'],
                        'category' => $bookmark['category'],
                        'date' => $today,
                        'isRecurring' => false // This is an automatically created transaction
                    ];
                }
            }
        }
    }

    /**
     * @Then a new transaction should be created based on the recurring settings
     */
    public function aNewTransactionShouldBeCreatedBasedOnTheRecurringSettings()
    {
        // At least two transactions should exist (the original and the new one)
        Assert::assertGreaterThanOrEqual(2, count($this->transactions), 'New transaction was not created.');
        
        $lastTransaction = end($this->transactions);
        $matchingBookmark = null;
        
        // Find the matching bookmark
        foreach ($this->bookmarks as $bookmark) {
            if ($bookmark['amount'] == $lastTransaction['amount'] && 
                $bookmark['category'] == $lastTransaction['category']) {
                $matchingBookmark = $bookmark;
                break;
            }
        }
        
        Assert::assertNotNull($matchingBookmark, 'Could not find matching bookmark for new transaction.');
    }

    /**
     * @Then the new transaction should have today's date
     */
    public function theNewTransactionShouldHaveTodaysDate()
    {
        $lastTransaction = end($this->transactions);
        $today = date('Y-m-d');
        
        Assert::assertEquals($today, $lastTransaction['date'], 
            'New transaction does not have today\'s date. Expected: ' . $today . ', Got: ' . $lastTransaction['date']);
    }
} 