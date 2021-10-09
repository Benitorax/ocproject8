<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Faker\Generator;
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

    private UserPasswordHasherInterface $passwordHasher;
    private Generator $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        foreach (self::USERS_DATA as $userData) {
            $user = $this->loadUser($userData);
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 9; $i++) {
            $task = $this->loadTask($users);
            $manager->persist($task);
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
     *
     * @param User[] $users
     */
    public function loadTask(array $users): Task
    {
        return (new Task())
            ->setTitle($this->faker->realText(mt_rand(15, 40), 5))
            ->setContent($this->faker->realText(mt_rand(60, 110), 5))
            ->setUser($users[array_rand($users)])
        ;
    }
}
