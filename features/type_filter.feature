Feature: Transaction Type Filtering
  As a user
  I want to filter transactions by their type
  So that I can view specific income or expense transactions

  Scenario: Filter transactions by type
    Given I am logged in
    When I am on the dashboard page
    And I select transaction type "income"
    Then I should see only income transactions in table
    And I should see the chart update with income data
    When I select transaction type "expense"
    Then I should see only expense transactions in table
    And I should see the chart update with expense data

  Scenario: View historical data by type
    Given I am logged in
    When I am on the dashboard page
    And I select transaction type "income"
    And I select data from 3 months ago
    Then I should see income transactions from the past 3 months
    And the filtered data should display without errors 