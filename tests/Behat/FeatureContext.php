<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Mink\Session;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

final class FeatureContext implements Context
{
    private static KernelInterface $kernel;
    private Session $session;
    private RouterInterface $router;

    private static ?Response $response = null;
    private static ?ContainerInterface $container = null;
    private static ?EntityManagerInterface $entityManager = null;

    public function __construct(KernelInterface $kernel, Session $session, RouterInterface $router)
    {
        self::$kernel = $kernel;
        $this->session = $session;
        $this->router = $router;
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
    }

    /**
     * Load fixtures.
     *
     * @BeforeScenario @fixtures
     */
    public function loadFixtures(): void
    {
        $loader = new ContainerAwareLoader(self::getContainer());
        $loader->loadFromDirectory(__DIR__ . '/../src/DataFixtures');

        $executor = new ORMExecutor(self::getEntityManager(), new ORMPurger());
        $executor->purge();
        $executor->execute($loader->getFixtures(), true);
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
}
