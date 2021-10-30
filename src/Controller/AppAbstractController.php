<?php

namespace App\Controller;

use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AppAbstractController extends AbstractController
{
    /**
     * Redirect to tasks index route with query or not.
     *
     * e.g. /tasks?tasks=todo
     */
    public function redirectToTasksIndex(Request $request): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $taskQuery = [];

        if (strpos($referer, '?')) {
            $stringQuery = (new UnicodeString($referer))->afterLast('?');
            $arrayQuery = $stringQuery->split('&');

            foreach ($arrayQuery as $query) {
                if ($query->startsWith('tasks=')) {
                    $taskQuery = ['tasks' => $query->afterLast('tasks=')->toString()];
                    break;
                }
            }
        }

        return $this->redirectToRoute('app_task_list', $taskQuery);
    }
}
