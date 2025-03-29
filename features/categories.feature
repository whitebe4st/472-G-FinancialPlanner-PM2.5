Feature: Financial Categories Management
  As a user
  I want to manage my financial categories
  So that I can organize my transactions better

  Scenario: Create new financial categories
    Given I am logged in
    When I go to category management page
    And I create a new category "Emergency Fund"
    And I create a new category "Healthcare"
    Then I should see "Emergency Fund" in the category list
    And I should see "Healthcare" in the category list

  Scenario: Update transaction category
    Given I am logged in
    And I have created category "Emergency Fund"
    When I go to transaction list
    And I select a transaction to edit
    And I change its category to "Emergency Fund"
    And I save the transaction
    Then the transaction should show "Emergency Fund" as its category
    And the financial report should update with the new category

  Scenario: View transactions by custom category
    Given I am logged in
    And I have transactions in "Emergency Fund" category
    When I filter transactions by category "Emergency Fund"
    Then I should see only transactions from "Emergency Fund" category 