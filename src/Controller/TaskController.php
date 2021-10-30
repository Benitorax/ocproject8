<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Service\TaskManager;
use App\Controller\AppAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AppAbstractController
{
    /**
     * @Route("/tasks", name="app_task_list")
     */
    public function index(Request $request, TaskManager $manager): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $manager->getAllTasks((string) $request->query->get('tasks')),
        ]);
    }

    /**
     * @Route("/tasks/create", name="app_task_create")
     */
    public function create(Request $request, TaskManager $manager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User */ $user = $this->getUser();
            $manager->saveNewTask($task, $user);
            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="app_task_edit")
     */
    public function edit(Task $task, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="app_task_toggle")
     */
    public function toggleTask(Task $task, Request $request, TaskManager $manager): Response
    {
        $manager->toggleTask($task);
        $this->addFlash('success', sprintf(
            'La tâche %s a bien été marquée comme %s.',
            $task->getTitle(),
            $task->isDone() ? "terminée" : "non terminée"
        ));

        return $this->redirectToTasksIndex($request);
    }

    /**
     * @Route("/tasks/{id}/delete", name="app_task_delete")
     */
    public function deleteTask(Task $task, Request $request, TaskManager $manager): Response
    {
        $this->denyAccessUnlessGranted('delete', $task);

        $manager->deleteTask($task);
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToTasksIndex($request);
    }
}
