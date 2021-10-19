<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private UserRepository $repository;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Save new user.
     */
    public function saveNewUser(User $user): void
    {
        $this->hashPassword($user);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Edit an user.
     */
    public function editUser(User $user): void
    {
        $this->hashPassword($user);
        $this->entityManager->flush();
    }

    /**
     * Hash password of user
     */
    public function hashPassword(User $user): void
    {
        $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
    }

    /**
     * Retrieve all users.
     *
     * @return User[]
     */
    public function getAllUsers()
    {
        return $this->repository->findAll();
    }
}
