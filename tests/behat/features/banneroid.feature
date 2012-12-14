Feature: Banneroid plugin standart features BDD
  Test base functionality of LiveStreet banneroid plugin standart

  Scenario: Check for banners on blogs
    Given I am on "/blog/gadgets/"

    Then the response status code should be 200
    Then I should see in element by css "#sidebar" any of values:
      | value |
      | /uploads/banneroid/livestreet_logo.jpeg |
      | <h1>Web development Stfalcon</h1> |

    Then I should see in element by css "body" any of values:
      | value |
      | /uploads/banneroid/jquery.jpeg |
      | <h1>Web development Header Banner</h1> |

    Then I should see in element by css "body" any of values:
      | value |
      | <h1 align="center">Development Footer Banner for Example Stfalcon</h1> |
      | /uploads/banneroid/stfalcon_logo_2.jpg |


  Scenario: Check for banners on homepage
    Then check is plugin active "banneroid"
    Given I load fixtures for plugin "banneroid"

    Given I am on homepage
    Then the response status code should be 200

    Then I should see in element by css "sidebar" values:
      | value |
      | /uploads/banneroid/livestreet_logo.jpeg |

    Then I should see in element by css "body" any of values:
      | value |
      | /uploads/banneroid/jquery.jpeg |

    Then I should see in element by css "body" any of values:
      | value |
      | <h1 align="center">Development Footer Banner for Example Stfalcon</h1> |

    Scenario: Check for cahging banners on blogs
        Then check is plugin active "banneroid"
        Given I load fixtures for plugin "banneroid"

        Given I am on "/blog/gadgets/"
        Then I should see "Web development Header Banner"
        And I should see "Web development Stfalcon"
        And I should see "Development Footer Banner for Example Stfalcon"

        Given I am on "/blog/gadgets/"
        Given I am on "/blog/gadgets/"
        Then the response should contain "/uploads/banneroid/jquery.jpeg"
        And the response should contain "/uploads/banneroid/stfalcon_logo_2.jpg"
        And the response should contain "/uploads/banneroid/livestreet_logo.jpeg"

