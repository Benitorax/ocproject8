<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class SecurityControllerTest extends AppWebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            'username' => 'Ross',
            'password' => '123456'
        ]);

        $this->assertResponseRedirects('/', 302);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Se connecter', [
            'username' => 'Ross',
            'password' => '123457'
        ]);
        $this->assertResponseRedirects('/login', 302);
        $client->followRedirect();
    }

    public function testLoginWhenComingFromAnotherPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $client->followRedirect();

        $client->submitForm('Se connecter', [
            'username' => 'Ross',
            'password' => '123456'
        ]);

        $this->assertResponseRedirects('http://localhost/users', 302);
    }

    public function testLoginWhenAlreadyLoggedIn(): void
    {
        $client = static::createClient();
        $user = $this->getAdminUser();
        $client->loginUser($user);
        $client->request('GET', '/login');
        $this->assertResponseRedirects('/', 302);
    }
}
