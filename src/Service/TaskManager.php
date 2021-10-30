<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    private TaskRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(TaskRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * Return an array of tasks from given filter.
     *
     * @return Task[]
     */
    public function getAllTasks(?string $filter = null)
    {
        switch ($filter) {
            case 'todo':
                return $this->repository->findAllTasks(false);
            case 'done':
                return $this->repository->findAllTasks(true);
            default:
                return $this->repository->findAllTasks();
        }
    }

    /**
     * Save a task.
     */
    public function saveNewTask(Task $task, User $user): void
    {
        $task->setUser($user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    /**
     * Toggle a task.
     */
    public function toggleTask(Task $task): void
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();
    }
}
