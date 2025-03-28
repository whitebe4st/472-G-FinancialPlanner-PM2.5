Feature: Transaction Bookmarks
  As a user
  I want to bookmark transactions
  So that I can quickly add them again in the future

Scenario: Marking a transaction as bookmarked
  Given I am on the transaction input page
  When I enter a valid amount
  And I select a category
  And I select a date
  And I check the "Bookmark this transaction" option
  And I click the save button
  Then the transaction should be saved
  And the transaction should be bookmarked
  And the transaction should appear in the bookmarks list

Scenario: Adding a transaction from bookmarks
  Given I am on the bookmarks page
  And I have at least one bookmarked transaction
  When I select a bookmarked transaction
  And I click "Add to transactions"
  And I select a date for the new transaction
  And I confirm the addition
  Then a new transaction should be created based on the bookmark
  And the new transaction should appear in the transaction list
  
Scenario: Adding multiple bookmarked transactions at once
  Given I am on the bookmarks page
  And I have multiple bookmarked transactions
  When I select multiple bookmarked transactions
  And I click "Add selected to transactions"
  And I select a date for the new transactions
  And I confirm the addition
  Then new transactions should be created based on the selected bookmarks
  And the new transactions should appear in the transaction list 