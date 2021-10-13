# Project as part of OpenClassrooms training

The project is developed with Symfony, its components and bundles. The only allowed third-party librairies are for test, fixtures and quality code.

It is a ToDo List website where users can:

-   View a list of tasks.
-   Create a task.
-   Edit a task.
-   Delete a task (only by task owner or admin user).

Where admin users can also:

-   View list of users.
-   Create an user.
-   Edit an user.

Only unauthenticated users can access to login page.

## Getting started
### Step 1: Configure environment variables
Copy the `.env file` in project directory, rename it to `.env.local` and configure the following variables for:
-   the database:
```false
DATABASE_URL=
 ```

### Step 2: Install components and librairies
Run the following command:
```false
composer install
```

### Step 3: Create database and tables
Run the following command:
```false
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

### Step 4: Create fixtures
Run the following command:
```false
php bin/console doctrine:fixtures:load
```

### Step 5: Launch the server
Run the following command:
```false
php -S 127.0.0.1:8000 -t public
```

Or with Symfony CLI:
```false
symfony serve -d
```

## Third-party librairies
-   [Twig](https://github.com/twigphp/Twig) for the template engine.
-   [PHPUnit](https://github.com/sebastianbergmann/phpunit) to run tests.
-   [Friends-of-Behat](https://github.com/FriendsOfBehat) to run tests with Behat.
-   [DBrekelmans/BDI](https://github.com/dbrekelmans/bdi) to install a browser driver (ChromeDriver or GeckoDriver).
-   [RobertFausk/Behat-Panther-Extension](https://github.com/robertfausk/behat-panther-extension) to use Panther with Behat.

## Clean code
-   [PHPStan](https://github.com/phpstan/phpstan): level 8
-   [PHPCS](https://github.com/squizlabs/PHP_CodeSniffer): PSR1 and PSR12

## Tests
### PHPUnit
Run this command to test with coverage:
```
./vendor/bin/phpunit --coverage-html var/report-test --stop-on-failure
``` 
The report will be accessible at `/var/report-test/index.html`

### Behat
Run this command to test:
```
./vendor/bin/behat
```

#### With Panther
Step 1: Run this command to install a browser driver:
```
./vendor/bin/bdi detect drivers
```
It will install GeckoDriver for Firefox or ChromeDriver for Chrome at directory `/drivers`.

Step 2: Add `@javascript` above scenario code:
```
    @javascript
    Scenario: Fail to login
        When I am on "/login"
        Then I should see "Login"
```

Step 3: Run Behat command
```
./vendor/bin/behat
```