<?php

namespace App\Tests\Service;

use App\Service\TaskManager;
use App\Repository\TaskRepository;
use App\Tests\Controller\AppWebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class TaskManagerTest extends AppWebTestCase
{
    /**
     * Test manager retrieves only tasks done, only not done or both.
     */
    public function testGetAllTasks(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(TaskRepository::class);
        $entityManager = $container->get(EntityManagerInterface::class);

        $manager = new TaskManager($repository, $entityManager);

        // without query string
        $tasks = $manager->getAllTasks();
        $this->assertSame(11, count($tasks));

        // with query string tasks=todo
        $tasks = $manager->getAllTasks('todo');
        $this->assertSame(11, count($tasks));
        foreach ($tasks as $task) {
            $this->assertFalse($task->isDone());
        }

        // with query string tasks=done
        $tasks = $manager->getAllTasks('done');
        foreach ($tasks as $task) {
            $this->assertTrue($task->isDone());
        }
    }

    /**
     * Test tasks already created are attached to anonymous user.
     */
    public function testAnonymousUserForTaskAlreadyCreated(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(TaskRepository::class);
        $tasks = $repository->findBy(['user' => null]);

        foreach ($tasks as $task) {
            $this->assertSame('Anonyme', $task->getUser()->getUsername());
        }
    }
}
