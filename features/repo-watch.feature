Feature: This is an important repository
  Scenario: I want to know when something happens with this repository
    Given I am an authenticated user
    When I watch the "add a valid repository name here" repository
    Then The "add the above valid repository name here" repository will list me as a watcher
    And I delete the repository called "add the above valid repository name here"