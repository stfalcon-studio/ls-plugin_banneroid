Feature: Banneroid plugin standart features BDD
  Test base functionality of LiveStreet banneroid plugin standart

    Scenario: Activated plugin and Load Fixtures  Banneroid Plugin
        Given I am activated plugin "banneroid"
        Given I load fixtures for plugin "banneroid"

        Given I am on homepage
            Then the response status code should be 200
        Given the following banners exist:
            | image                | text                                |
            | stfalcon_logo_2.jpg  | Banner footer Stfalcon - contacts  |
            |livestreet_logo.jpeg  | Banner sidebar Stfalcon             |
            |jquery.jpeg           | Banner header Stfalcon - blog       |

        Given I am on "/blog/gadgets/"
            Then the response status code should be 200
        Given the following banners exist:
            | image                | text                                |
            | stfalcon_logo_2.jpg  | Banner footer Stfalcon - contacts   |
            |livestreet_logo.jpeg  | Banner sidebar Stfalcon             |
            |jquery.jpeg           | Banner header Stfalcon - blog       |

        Given I am on "blog/gadgets/1.html"
            Then the response status code should be 200
        Given the following banners exist:
            | image                | text                                |
            |ZF2.jpeg              | Web development                     |
            |stfalcon_logo_2.jpg   | Banner footer Stfalcon - contacts   |
            |livestreet_logo.jpeg  | Banner sidebar Stfalcon             |
            |jquery.jpeg           | Banner header Stfalcon - blog       |