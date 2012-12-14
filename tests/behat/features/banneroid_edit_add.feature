Feature: Banneroid plugin features add && edit banner
  Test functionality of LiveStreet banneroid plugin add & edit banner

  @mink:selenium2
    Scenario: Check banner statistic
      Then check is plugin active "banneroid"
      Given I load fixtures for plugin "banneroid"
      Given I am on homepage

      Then I want to login as "admin"

      Given I am on "/banneroid/stats-banners/5/"
      Then I should see in element by css "content" values:
      | value |
      | <td>2</td> |

  @mink:selenium2
    Scenario: Change existing banner
      Then check is plugin active "banneroid"
      Given I load fixtures for plugin "banneroid"
      Given I am on homepage

      Then I want to login as "admin"
      Given I am on "/banneroid/edit/5/"

      When I fill in "banner_name" with "Changed banner name"
      Then I press element by css "input[value='4']"
      Then I press element by css "input[name='banner_place[]'][value='2']"
      When I uncheck "banner_is_active"

      When I press element by css "input[name='submit_banner']"
      Then I wait "1000"
      Then I should see "Changed banner name"
      Then I should not see "На всех страницах(Сайд бар) Блоги(Сайд бар)"


  @mink:selenium2
    Scenario: Create new banner
      Then check is plugin active "banneroid"
      Given I load fixtures for plugin "banneroid"
      Given I am on homepage

      Then I want to login as "admin"

      Given I am on "/banneroid/add/"
      When I fill in "banner_name" with "Add new banner GOOGLE"
      When I fill in "banner_url" with "http://google.com"

      When I press element by css "input[value='kind_html']"
      When I fill in "banner_html" with "<h2>This is just created bunner</h2>"

      When I check "banner_is_active"
      When I check "banner_place[]"

      When I press element by css "input[name='submit_banner']"
      Then I wait "1000"

      Given I am on "/banneroid/"
      Then I should see "Add new banner GOOGLE"