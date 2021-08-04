<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskManager
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
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
                return $this->repository->findAll();
        }
    }
}
