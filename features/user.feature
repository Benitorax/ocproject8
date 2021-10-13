Feature:
    In order to manage users
    As an admin user
    I need to be able to add and edit users

    Scenario: Access denied if not logged in
        When I go to "/users"
        Then I should not see "Liste des utilisateurs"
        But I should see "Se connecter"

    Scenario: Access denied if not admin
        Given I am logged in as "Rachel"
        When I am on "/"
        Then I should not see "Liste d'utilisateurs"
        When I go to "/users"
        Then the response status code should be 403

    Scenario: Link is visible when logged as admin
        Given I am logged in as "Ross"
        When I am on "/"
        Then I should see "Liste d'utilisateurs"
        When I follow "Liste d'utilisateurs"
        Then I should see "Liste des utilisateurs"

    Scenario: Create an user with user role
        Given I am logged in as "Ross"
        When I am on "/users"
        Then I should see "Créer un utilisateur"
        When I follow "Créer un utilisateur"
        And I fill in "Nom d'utilisateur" with "Phoebe"
        And I fill in "Mot de passe" with "456789"
        And I fill in "Tapez le mot de passe à nouveau" with "456789"
        And I fill in "Adresse email" with "phoebe@example.com"
        And I select "rôle utilisateur" from "Rôle"
        And I press "Ajouter"
        Then the "Phoebe" row should have "Utilisateur" as role
        Then the "Phoebe" row should have "phoebe@example.com" as email

    Scenario: Create an user with admin role
        Given I am logged in as "Ross"
        When I am on "/users"
        Then I should see "Créer un utilisateur"
        When I follow "Créer un utilisateur"
        And I fill in "Nom d'utilisateur" with "Chandler"
        And I fill in "Mot de passe" with "456789"
        And I fill in "Tapez le mot de passe à nouveau" with "456789"
        And I fill in "Adresse email" with "chandler@example.com"
        And I select "rôle administrateur" from "Rôle"
        And I press "Ajouter"
        Then the "Chandler" row should have "Administrateur" as role
        Then the "Chandler" row should have "chandler@example.com" as email

    Scenario: Edit an user with success
        Given I am logged in as "Ross"
        And I am on "/users"
        When I click on button "Editer" for user "Chandler"
        And I fill in "Mot de passe" with "456789"
        And I fill in "Tapez le mot de passe à nouveau" with "456789"
        And I fill in "Adresse email" with "chandler.bing@example.com"
        And I select "rôle utilisateur" from "Rôle"
        And I press "Modifier"
        Then the "Chandler" row should have "Utilisateur" as role
        Then the "Chandler" row should have "chandler.bing@example.com" as email