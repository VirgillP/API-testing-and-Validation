Feature: Search repositories
  Scenario: I want to get a list of repositories that reference Behat
    Given I am anonymous user
    When I search for "VirgillP"
    Then I expect a 200 response code
    And I expect at least 1 result

