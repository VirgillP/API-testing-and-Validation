Feature: Get my repositories
  Scenario: I want a list of my repositories
    Given I am an authenticated user
    When I request a list of repositories
    Then The results should include repository name "forecast_web_app"
