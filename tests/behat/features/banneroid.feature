Feature: Banneroid plugin standart features BDD
  Test base functionality of LiveStreet banneroid plugin standart

    Scenario: Authorization Admininstator
        Given I am activated plugin "banneroid"
        Given I load fixtures for plugin "banneroid"


#        Given I am on "/blog/gadgets/"
#            Then the response status code should be 200


#        Given the following banners exist:
#            | image         | text                                                                 |
#            | http://livestreet_101.ru.work/uploads/banneroid/jquery.jpeg | Web development Header Banner                                        |
#            |               | Web development Stfalcon                                             |
#            | http://livestreet_101.ru.work/uploads/banneroid/stfalcon_logo_2.jpg  | Development Footer Banner for Example Stfalcon                        |