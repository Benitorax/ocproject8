# Contributing to the project

-   Step 1 - Check existing Issues and Pull Requests to avoid duplication.
-   Step 2 - Clone the project with this command:
```false
git clone git@github.com:Benitorax/ocproject8.git
```

-   Step 3 - Follow the readme instructions to make the application functional.
-   Step 4 - Create a Pull Request at branch “dev”.
-   Step 5 - Create a Topic Branch if you need to work on a pull request:
```false
git checkout -b BRANCH_NAME dev
```

-   Step 6 - Clean your code with PHPStan and PHPCS commands:
```false
./vendor/bin/phpcs
./vendor/bin/phpcbf
./vendor/bin/phpstan
```
Thanks to configuration files (phpcs.xml.dist and phpstan.neon.dist at the project root), you don't need to configure the rulesets and directories.

-   Step 7 - Make test with PHPUnit and run the command below to have at least 90% of coverage:
```false
./vendor/bin/phpunit --coverage-html var/report-test/
```

-   Step 8 - Rebase your code before pushing:
```false
git checkout dev
git fetch upstream
git merge upstream/dev
git checkout BRANCH_NAME
git rebase dev
git push --force origin BRANCH_NAME
```