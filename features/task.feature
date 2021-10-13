Feature:
    In order to manage tasks
    As an user
    I need to be able to add, edit, mark as done or to do a task

    Scenario: Create a task from homepage
        Given I am logged in as "Rachel"
        And I am on "/"
        When I follow "Créer une nouvelle tâche"
        And I fill in "Titre" with "Do the housework"
        And I fill in "Contenu" with "Bedroom, living room, kitchen"
        And I press "Ajouter"
        Then I should see "Superbe ! La tâche a bien été ajoutée."
        And I should see "Do the housework"
        And I should see "Bedroom, living room, kitchen"

    Scenario: Create a task from tasks page
        Given I am logged in as "Rachel"
        And I am on "/tasks"
        When I follow "Créer une tâche"
        And I fill in "Titre" with "Wash the dishes"
        And I fill in "Contenu" with "Plates, glasses, spoons, forks"
        And I press "Ajouter"
        Then I should see "Superbe ! La tâche a bien été ajoutée."
        And I should see "Wash the dishes"
        And I should see "Plates, glasses, spoons, forks"

    Scenario: Edit a task with success
        Given I am logged in as "Rachel"
        And I am on "/tasks"
        When I click on task "Wash the dishes"
        And I fill in "Titre" with "Wash the big dishes"
        And I fill in "Contenu" with "Big plates, big glasses, big spoons, big forks"
        And I press "Modifier"
        Then I should see "Superbe ! La tâche a bien été modifiée."
        And I should see "Wash the big dishes"
        And I should see "Big plates, big glasses, big spoons, big forks"
        And I should not see "Wash the dishes"

    Scenario: Mark a task as done
        Given I am logged in as "Rachel"
        And I am on "/tasks?tasks=todo"
        When I click on "Marquer comme faite" for task "Wash the big dishes"
        Then I should see "Superbe ! La tâche Wash the big dishes a bien été marquée comme terminée."
        When I go to "/tasks?tasks=done"
        Then I should see "Wash the big dishes"

    Scenario: Mark a task as to do
        Given I am logged in as "Rachel"
        And I am on "/tasks?tasks=done"
        When I click on "Marquer non terminée" for task "Wash the big dishes"
        Then I should see "Superbe ! La tâche Wash the big dishes a bien été marquée comme non terminée."
        When I go to "/tasks?tasks=todo"
        Then I should see "Wash the big dishes"

    Scenario: Delete a task as owner
        Given I am logged in as "Rachel"
        And I am on "/tasks"
        When I click on "Supprimer" for task "Wash the big dishes"
        Then I should see "Superbe ! La tâche a bien été supprimée."
        And I should not see "Wash the big dishes"

    Scenario: Delete a task as not owner
        Given I am logged in as "Monica"
        When I am on "/tasks"
        Then I should not see "Supprimer" for task "Do the housework"

    Scenario: Delete a task as admin
        Given I am logged in as "Ross"
        And I am on "/tasks"
        When I click on "Supprimer" for task "Do the housework"
        Then I should see "Superbe ! La tâche a bien été supprimée."
        And I should not see "Do the housework"
