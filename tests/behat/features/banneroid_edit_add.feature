Feature: Banneroid plugin features add && edit banner
  Test functionality of LiveStreet banneroid plugin add & edit banner

@mink:selenium2
    Scenario: Activated plugin and Load Fixtures  Banneroid Plugin
       Given I am activated plugin "banneroid"
       #Given I load fixtures for plugin "banneroid"

    Given I am on "/login/"
    Then I want to login administrator

    Given I am on "/banneroid/edit/1/"
        When I fill in "banner_name" with "(1)H->stfalcon  PortFolio- Edit Banner"
        When I fill in "banner_url" with "http://stfalcon.com/portfolio/web-development"
        When I fill in "banneroid_html" with "<h2>Разработка веб-проектов</h2>"
        When I uncheck "banner_is_active"
        When I press "submit_banner"
         Then I wait "2000"
    Given I am on "/banneroid/add/"
        When I fill in "banner_name" with "Add new banner GOOGLE"
        When I fill in "banner_url" with "http://google.com"
        When I attach the file to path  "/plugins/banneroid/tests/behat/fixtures/image/JQuery_UI_Logo.jpeg" to "banner_image"
        When I check "banner_is_active"
        When I check "banner_place[]"
        When I press "submit_banner"
         Then I wait "3000"

    Given I am on "/banneroid/"
         Then I should see "Add new banner GOOGLE"

         Then I wait "3000"