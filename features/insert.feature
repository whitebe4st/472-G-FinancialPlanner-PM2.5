Feature: Transaction Input
  As a user
  I want to input transaction amounts, categories, and dates
  So that I can track my financial activities

Scenario: Successfully adding a transaction
  Given I am on the transaction input page
  When I enter a valid amount
  And I select a category
  And I select a date
  And I click the save button
  Then the transaction should be saved
  And the transaction should appear in the transaction list

Scenario: Validation for incomplete transaction data
  Given I am on the transaction input page
  When I leave the amount field empty
  And I click the save button
  Then I should see an error message indicating amount is required
  
  Given I am on the transaction input page
  When I enter a valid amount
  And I do not select a category
  And I click the save button
  Then I should see an error message indicating category is required
  
  Given I am on the transaction input page
  When I enter a valid amount
  And I select a category
  And I do not select a date
  And I click the save button
  Then I should see an error message indicating date is required 