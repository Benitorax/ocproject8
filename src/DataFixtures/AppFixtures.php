<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const USERS_DATA = [
        ['Ross', '123456', 'ross@example.com', 'ROLE_ADMIN'],
        ['Rachel', '123456', 'rachel@example.com', 'ROLE_USER'],
        ['Monica', '123456', 'monica@example.com', 'ROLE_USER'],
    ];

    private array $users = [];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS_DATA as $userData) {
            $user = $this->loadUser($userData);
            $manager->persist($user);
            $this->users[$user->getUsername()]['user'] = $user;

            for ($i = 0; $i < 3; $i++) {
                $task = $this->loadTaskForUser($user, $i);
                $manager->persist($task);
            }
        }


        $manager->flush();
    }

    /**
     * Return a User object.
     */
    public function loadUser(array $userData): User
    {
        $user = new User();

        return $user
            ->setUsername($userData[0])
            ->setPassword($this->passwordHasher->hashPassword($user, $userData[1]))
            ->setEmail($userData[2])
            ->setRoles([$userData[3]])
        ;
    }

    /**
     * Return a Task object from a given user.
     */
    public function loadTaskForUser(User $user, int $counter): Task
    {
        return (new Task())
            ->setTitle('title' . $counter . ' of ' . $user->getUsername())
            ->setContent('content' . $counter . ' of ' . $user->getUsername())
            ->setUser($user)
        ;
    }
}
