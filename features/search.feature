Feature: Search repositories
  Scenario: I want to get a list of repositories that reference <GitHub username>
    Given I am an anonymous user
    When I search for "add a valid repository name here or i.e. github username"
    Then I expect a 200 response code
    And I expect at least 1 result

