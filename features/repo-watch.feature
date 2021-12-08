Feature: This is an important repository
  Scenario: I want to know when something happens with this repository
    Given I am an authenticated user
    When I watch the "coding" repository
    Then The "coding" repository will list me as a watcher
    And I delete the repository called "coding"