Feature: Recurring Transactions
  As a user
  I want to set up recurring transactions
  So that I can automatically track regular income and expenses

Scenario: Marking a transaction as recurring
  Given I am on the transaction input page
  When I enter a valid amount
  And I select a category
  And I select a date
  And I check the "Make this a recurring transaction" option
  And I click the save button
  Then the transaction should be saved
  And the transaction should be marked as recurring
  And the transaction should appear in the bookmarks list

Scenario: Setting up recurring transaction frequency
  Given I am on the bookmarks page
  When I select a bookmarked transaction
  And I set the frequency to "Monthly"
  And I set the repeat day to "15"
  And I click the save button
  Then the recurring transaction settings should be updated

Scenario: Automatic transaction creation
  Given I have a recurring transaction set to "Monthly" on day "15"
  When the system runs the recurring transaction process
  Then a new transaction should be created based on the recurring settings
  And the new transaction should have today's date 