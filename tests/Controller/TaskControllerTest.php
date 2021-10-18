<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class TaskControllerTest extends AppWebTestCase
{
    public function testTaskIndex(): void
    {
        $client = static::createClient();

        // not logged in
        $client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login', 302);

        // logged in
        $user = $this->getAdminUser();
        $client->loginUser($user);
        $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();

        // with query string tasks=todo
        $client->request('GET', '/tasks?tasks=todo');
        $this->assertResponseIsSuccessful();

        // with query string tasks=done
        $client->request('GET', '/tasks?tasks=done');
        $this->assertResponseIsSuccessful();
    }

    public function testTaskCreate(): void
    {
        $client = static::createClient();

        // not logged in
        $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects('/login', 302);

        // logged in
        $user = $this->getAdminUser();
        $client->loginUser($user);
        $client->request('GET', '/tasks/create');
        $client->submitForm('Ajouter', [
            'task[title]' => 'My task title',
            'task[content]' => 'My task content'
        ]);
        $this->assertResponseRedirects('/tasks', 302);
    }

    public function testTaskEditWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $user = $this->getUser('Rachel');
        $task = $this->getTasksFromUser($user)[0];
        $editUrl = '/tasks/' . $task->getId() . '/edit';

        // not logged in
        $client->request('GET', $editUrl);
        $this->assertResponseRedirects('/login', 302);
    }

    public function testTaskEdit(): void
    {
        $client = static::createClient();
        $owner = $this->getUser('Rachel');
        $task = $this->getTasksFromUser($owner)[0];
        $editUrl = '/tasks/' . $task->getId() . '/edit';

        // logged as owner
        $client->loginUser($owner);
        $client->request('GET', $editUrl);
        $client->submitForm('Modifier', [
            'task[title]' => 'My task title',
            'task[content]' => 'My task content'
        ]);
        $this->assertResponseRedirects('/tasks', 302);

        // logged as Monica (not owner)
        $anotherUser = $this->getUser('Monica');
        $client->loginUser($anotherUser);
        $client->request('GET', $editUrl);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testTaskEditWhenAdmin(): void
    {
        $client = static::createClient();
        $owner = $this->getUser('Rachel');
        $task = $this->getTasksFromUser($owner)[0];
        $editUrl = '/tasks/' . $task->getId() . '/edit';

        // logged as admin
        $admin = $this->getUser('Ross');
        $client->loginUser($admin);
        $client->request('GET', $editUrl);
        $client->submitForm('Modifier', [
            'task[title]' => 'My task title for admin',
            'task[content]' => 'My task content for admin'
        ]);
        $this->assertResponseRedirects('/tasks', 302);
    }

    public function testTaskToggle(): void
    {
        $client = static::createClient();
        $owner = $this->getUser('Rachel');
        $task = $this->getTasksFromUser($owner)[0];
        $toggleUrl = '/tasks/' . $task->getId() . '/toggle';

        // logged as owner
        $client->loginUser($owner);
        $client->request('GET', $toggleUrl);
        $this->assertResponseRedirects(null, 302);

        // logged as Monica (not owner)
        $anotherUser = $this->getUser('Monica');
        $client->loginUser($anotherUser);
        $client->request('GET', $toggleUrl);
        $this->assertResponseRedirects(null, 302);
    }

    public function testTaskDelete(): void
    {
        $client = static::createClient();
        $owner = $this->getUser('Rachel');
        $task = $this->getTasksFromUser($owner)[0];
        $deleteUrl = '/tasks/' . $task->getId() . '/delete';

        // logged as Monica (not owner)
        $user = $this->getUser('Monica');
        $client->loginUser($user);
        $client->request('POST', $deleteUrl);
        $this->assertResponseStatusCodeSame(403);

        // logged as owner
        $client->loginUser($owner);
        $client->request('POST', $deleteUrl);
        $this->assertResponseRedirects('/tasks', 302);
    }

    public function testTaskDeleteWhenAdmin(): void
    {
        $client = static::createClient();
        $owner = $this->getUser('Monica');
        $task = $this->getTasksFromUser($owner)[0];
        $deleteUrl = '/tasks/' . $task->getId() . '/delete';

        // logged as admin
        $user = $this->getAdminUser();
        $client->loginUser($user);
        $client->request('POST', $deleteUrl);
        $this->assertResponseRedirects('/tasks', 302);
    }
}
