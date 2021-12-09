Feature: I want to create a new repository
  Scenario: I want to create a new repository
    Given I am an authenticated user
    When I create the "add a valid repository name here" repository
    And I request a list of my repositories
    Then The results should include a repository named "add the above valid repository name here"
