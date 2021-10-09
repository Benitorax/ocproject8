<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppWebTestCase extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::createClient();
        self::initDatabase();
        self::purgeDatabase();
        self::loadFixtures();
        self::ensureKernelShutdown();
    }

    /**
     * Create database schema.
     */
    private static function initDatabase(): void
    {
        $entityManager = self::getEntityManager();
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metaData);
    }

    /**
     * Load fixtures.
     */
    private static function loadFixtures(): void
    {
        self::getService(AppFixtures::class)->load(self::getEntityManager());
    }

    /**
     * Return a service from container.
     *
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    private static function getService($id)
    {
        /** @var T */
        return static::getContainer()->get((string) $id);
    }

    /**
     * Return an entity manager.
     */
    private static function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        $entityManager = self::getService(EntityManagerInterface::class);

        return $entityManager;
    }

    /**
     * Purge database.
     */
    private static function purgeDatabase(): void
    {
        $purger = new ORMPurger(self::getEntityManager());
        $purger->purge();
    }

    /**
     * Return an User object from given username.
     */
    public static function getUser(string $username): User
    {
        /** @var User */
        return self::getService(UserRepository::class)->findOneBy(['username' => $username]);
    }

    /**
     * Return an admin User object.
     */
    public static function getAdminUser(): User
    {
        /** @var User */
        return self::getService(UserRepository::class)->findOneAdmin();
    }

    /**
     * Return Task objects from given user.
     *
     * @return Task[]
     */
    public function getTasksFromUser(User $user)
    {
        return self::getService(TaskRepository::class)->findBy(['user' => $user]);
    }
}
