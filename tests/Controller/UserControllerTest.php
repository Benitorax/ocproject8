<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class UserControllerTest extends AppWebTestCase
{
    public function testUserIndex(): void
    {
        $client = static::createClient();

        // not logged in
        $client->request('GET', '/users');
        $this->assertResponseRedirects('/login', 302);

        // logged in as not admin
        $user = $this->getUser('Rachel');
        $client->loginUser($user);
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(403);

        // logged in as admin
        $user = $this->getUser('Ross');
        $client->loginUser($user);
        $client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
    }

    public function testUserCreate(): void
    {
        $client = static::createClient();

        // not logged in
        $client->request('GET', '/users/create');
        $this->assertResponseRedirects('/login', 302);

        // logged in as not admin
        $user = $this->getUser('Rachel');
        $client->loginUser($user);
        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(403);

        // logged in as admin
        $adminUser = $this->getUser('Ross');
        $client->loginUser($adminUser);
        $client->request('GET', '/users/create');
        $client->submitForm('Ajouter', [
            'user[username]' => 'Chandler',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
            'user[email]' => 'chandler@example.com',
            'user[roles]' => 'ROLE_USER',
        ]);
        $this->assertResponseRedirects('/users', 302);
    }

    public function testUserEdit(): void
    {
        $client = static::createClient();
        $user = $this->getUser('Rachel');
        $editUrl = '/users/' . $user->getId() . '/edit';

        // not logged in
        $client->request('GET', $editUrl);
        $this->assertResponseRedirects('/login', 302);

        // logged in as not admin
        $user = $this->getUser('Rachel');
        $client->loginUser($user);
        $client->request('GET', $editUrl);
        $this->assertResponseStatusCodeSame(403);

        // logged in as admin
        $adminUser = $this->getUser('Ross');
        $client->loginUser($adminUser);
        $client->request('GET', $editUrl);
        $client->submitForm('Modifier', [
            'user[username]' => 'Phoebe',
            'user[password][first]' => '123457',
            'user[password][second]' => '123457',
            'user[email]' => 'phoebe@example.com',
            'user[roles]' => 'ROLE_ADMIN',
        ]);
        $this->assertResponseRedirects('/users', 302);
    }
}
