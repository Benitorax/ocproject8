<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\DataFixtures\AppFixtures;
use Behat\Mink\Session;
use PHPUnit\Framework\Assert;
use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class FeatureContext implements Context
{
    private static KernelInterface $kernel;
    private Session $session;
    private RouterInterface $router;
    private static UserPasswordHasherInterface $passwordHasher;

    private static ?Response $response = null;
    private static ?ContainerInterface $container = null;
    private static ?EntityManagerInterface $entityManager = null;

    public function __construct(
        KernelInterface $kernel,
        Session $session,
        RouterInterface $router,
        UserPasswordHasherInterface $passwordHasher
    ) {
        self::$kernel = $kernel;
        $this->session = $session;
        $this->router = $router;
        self::$passwordHasher = $passwordHasher;
    }

    /**
     * Return the container.
     */
    public static function getContainer(): ContainerInterface
    {
        if (null !== self::$container) {
            return self::$container;
        }

        return self::$container = self::$kernel->getContainer();
    }

    /**
     * Return the entity manager.
     */
    public static function getEntityManager(): EntityManagerInterface
    {
        if (null !== self::$entityManager) {
            return self::$entityManager;
        }

        /** @phpstan-ignore-next-line */
        return self::$entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @BeforeSuite
     */
    public static function bootstrapSymfony(): void
    {
        self::$kernel->boot();
        self::loadFixtures();
    }

    /**
     * Load fixtures.
     *
     * @fixtures
     */
    public static function loadFixtures(): void
    {
        // $loader = new ContainerAwareLoader(self::getContainer());
        // $loader->loadFromDirectory(__DIR__ . '\\..\\..\\src\\DataFixtures');
        // $executor = new ORMExecutor(self::getEntityManager(), new ORMPurger());
        // $executor->purge();
        // $executor->execute($loader->getFixtures(), true);
        (new ORMPurger(self::getEntityManager()))->purge();
        (new AppFixtures(self::$passwordHasher))->load(self::getEntityManager());
    }

    /**
     * @Given I am logged in as :username
     */
    public function iAmLoggedInAs(string $username): void
    {
        $this->session->visit('/login');
        $page = $this->session->getPage();
        $page->fillField('Nom d\'utilisateur', $username);
        $page->fillField('Mot de passe', '123456');
        $page->pressButton('Se connecter');
    }

    /**
     * Return a row node element from given text.
     */
    private function findRowByText(string $rowText): NodeElement
    {
        $row = $this->session->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        Assert::assertNotNull($row, 'Cannot find a table row with this text!');
        /** @var NodeElement $row */
        return $row;
    }

    /**
     * @When I click :linkText in the :rowText row
     */
    public function iClickInTheRow(string $linkText, string $rowText): void
    {
        $row = $this->findRowByText($rowText);
        $link = $row->findLink($linkText);
        Assert::assertNotNull($link, 'Cannot find link in row with text ' . $linkText);
        /** @var NodeElement $link */
        $link->click();
    }

    /**
     * @Then the :username row should have :text as role
     * @Then the :username row should have :text as email
     */
    public function theUserRowShouldHaveText(string $username, string $text): void
    {
        $row = $this->session->getPage()->find('css', sprintf('table tr:contains("%s")', $username));
        Assert::assertNotNull($row, sprintf('Cannot find a table row with this text: %s', $username));

        /** @var NodeElement $row */
        Assert::assertStringContainsString(
            $text,
            $row->getHtml(),
            sprintf('Cannot find the following text: %s', $text)
        );
    }

    /**
     * @When I click on button :buttonText for user :username
     */
    public function iClickOnButtonForUser(string $buttonText, string $username): void
    {
        $row = $this->session->getPage()->find('css', sprintf('table tr:contains("%s")', $username));
        Assert::assertNotNull($row, sprintf('Cannot find a table row with this text: %s', $username));
        /** @var NodeElement $row */
        $row->clickLink($buttonText);
    }

    /**
     * @When I click on :buttonText for task :task
     */
    public function iClickOnButtonForTask(string $buttonText, string $task): void
    {
        $div = $this->session->getPage()->find('css', sprintf('.thumbnail:contains("%s")', $task));
        Assert::assertNotNull($div, sprintf(
            'Cannot find element with class "thumbnail" which contains text "%s"',
            $task
        ));
        /** @var NodeElement $div */
        $div->pressButton($buttonText);
    }

    /**
     * @When I click on task :task
     */
    public function iClickOnTask(string $task): void
    {
        $this->session->getPage()->clickLink($task);
    }

    /**
     * @Then I should not see :buttonText for task :task
     */
    public function ishouldNotSeeButtonForTask(string $buttonText, string $task): void
    {
        $div = $this->session->getPage()->find('css', sprintf('.thumbnail:contains("%s")', $task));
        Assert::assertNotNull($div, sprintf(
            'Cannot find element with class "thumbnail" which contains text "%s"',
            $task
        ));
        /** @var NodeElement $div */
        $button = $div->findButton($buttonText);
        Assert::assertNull($button, sprintf(
            'Button element which contains "%s" should not appear',
            $task
        ));
    }
}
