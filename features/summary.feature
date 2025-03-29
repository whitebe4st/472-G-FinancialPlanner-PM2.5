Feature: Financial Dashboard
  As a user
  I want to see a summary of my monthly income, expenses, and remaining balance
  So that I can easily understand my financial status

Scenario: Viewing monthly financial summary
  Given I am logged in
  When I visit the dashboard page
  Then I should see the current month's total income
  And I should see the current month's total expenses
  And I should see the current month's remaining balance

Scenario: Viewing financial data visualization
  Given I am logged in
  When I visit the dashboard page
  Then I should see a chart comparing income and expenses

Scenario: Changing the month for financial summary
  Given I am logged in
  And I am on the dashboard page
  When I select a different month from the month selector
  Then the financial summary should update with data for the selected month
