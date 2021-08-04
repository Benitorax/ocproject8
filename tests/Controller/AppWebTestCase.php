<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\AppFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppWebTestCase extends WebTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
        $this->purgeDatabase();
        $this->loadFixtures();
        self::ensureKernelShutdown();
    }

    /**
     * Load fixtures.
     */
    private function loadFixtures(): void
    {
        $this->getService(AppFixtures::class)->load($this->getEntityManager());
    }

    /**
     * Return a service from container.
     *
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    private function getService($id)
    {
        /** @var T */
        return static::getContainer()->get((string) $id);
    }

    /**
     * Return an entity manager.
     */
    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        $entityManager = $this->getService(EntityManagerInterface::class);

        return $entityManager;
    }

    /**
     * Purge database.
     */
    private function purgeDatabase(): void
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * Return an User object from given username.
     */
    public function getUser(string $username): User
    {
        /** @var User */
        return $this->getService(UserRepository::class)->findOneBy(['username' => $username]);
    }

    /**
     * Return Task objects from given user.
     *
     * @return Task[]
     */
    public function getTasksFromUser(User $user)
    {
        return $this->getService(TaskRepository::class)->findBy(['user' => $user]);
    }
}
