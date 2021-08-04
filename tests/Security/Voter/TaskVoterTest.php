<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Security\Voter\TaskVoter;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class TaskVoterTest extends TestCase
{
    private TaskVoter $voter;
    private Task $task;

    public function setUp(): void
    {
        $this->voter = new TaskVoter();
        $this->task = (new Task())->setUser(new User());
    }

    public function testTaskVoterWithInvalidUser(): void
    {
        $isAllowed = $this->voter->vote(new NullToken(), $this->task, ['delete']);
        $this->assertSame(-1, $isAllowed);
    }
}
