<?php

namespace App\Tests\Service;

use App\Service\TaskManager;
use App\Repository\TaskRepository;
use App\Tests\Controller\AppWebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class TaskManagerTest extends AppWebTestCase
{
    public function testGetAllTasks(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(TaskRepository::class);
        $entityManager = $container->get(EntityManagerInterface::class);

        $manager = new TaskManager($repository, $entityManager);

        // without query string
        $tasks = $manager->getAllTasks();
        $this->assertSame(9, count($tasks));

        // with query string tasks=todo
        $tasks = $manager->getAllTasks('todo');
        $this->assertSame(9, count($tasks));

        // with query string tasks=done
        $tasks = $manager->getAllTasks('done');
        $this->assertSame(0, count($tasks));
    }
}
