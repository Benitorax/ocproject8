Feature:
    In order to have access to the site
    As a user
    I need to be able to login and logout

    Scenario: Fail to login
        Given I am on "/login"
        Then I should see "Nom d'utilisateur"
        When I fill in "Nom d'utilisateur" with "Ross"
        And I fill in "Mot de passe" with "987654"
        And I press "Se connecter"
        Then I should see "Invalid credentials"

    Scenario: Login with success from homepage
        Given I am on "/"
        Then I should not see "Bienvenue sur Todo List"
        When I fill in "Nom d'utilisateur" with "Ross"
        And I fill in "Mot de passe" with "123456"
        And I press "Se connecter"
        Then I should see "Bienvenue sur Todo List"

    Scenario: Logout with success
        Given I am logged in as "Ross"
        And I am on "/"
        Then I should see "Se déconnecter"
        And I follow "Se déconnecter"
        Then I should see "Se connecter"

