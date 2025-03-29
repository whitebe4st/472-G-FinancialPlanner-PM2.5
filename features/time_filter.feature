Feature: Time Period Filtering
  As a user
  I want to filter data by time period
  So that I can view financial information for specific time ranges

  Scenario: Filter data by different time periods
    Given I am logged in
    When I am on the dashboard page
    And I select "daily" view
    Then I should see the filtered transactions in table
    And I should see the chart update accordingly
    When I select "weekly" view
    Then I should see the filtered transactions in table
    And I should see the chart update accordingly
    When I select "monthly" view
    Then I should see the filtered transactions in table
    And I should see the chart update accordingly

  Scenario: View historical data
    Given I am logged in
    When I am on the dashboard page
    And I select data from 3 months ago
    Then I should see transactions from the past 3 months
    And the data should display without errors 