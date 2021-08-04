<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AppWebTestCase;

class DefaultControllerTest extends AppWebTestCase
{
    public function testHomepageWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testHomepageWhenLoggedIn(): void
    {
        $client = static::createClient();
        $user = $this->getUser('Ross');
        $client->loginUser($user);
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
