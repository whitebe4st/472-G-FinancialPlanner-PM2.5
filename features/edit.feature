Feature: Transaction Edit and Delete
  As a user
  I want to edit or delete my transaction records
  So that I can keep my financial data accurate and up-to-date

Scenario: Editing a transaction
  Given I am on the transaction list page
  When I select a transaction to edit
  And I change the amount to "150.00"
  And I change the category to "Entertainment"
  And I change the date to tomorrow
  And I click the save button
  Then the transaction should be updated with the new values
  And I should see the updated transaction in the list

Scenario: Deleting a transaction
  Given I am on the transaction list page
  When I select a transaction to delete
  Then I should see a confirmation dialog
  When I confirm the deletion
  Then the transaction should be removed from the database
  And the transaction should no longer appear in the list

Scenario: Canceling transaction deletion
  Given I am on the transaction list page
  When I select a transaction to delete
  Then I should see a confirmation dialog
  When I cancel the deletion
  Then the transaction should still appear in the list
