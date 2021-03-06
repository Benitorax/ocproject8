<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['delete', 'edit'])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        /** @var Task */
        $task = $subject;

        switch ($attribute) {
            case 'delete':
                return $this->canDelete($task, $user);
            case 'edit':
                return $this->canEdit($task, $user);
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    private function canDelete(Task $task, UserInterface $user): bool
    {
        return $user === $task->getUser();
    }

    private function canEdit(Task $task, UserInterface $user): bool
    {
        return $this->canDelete($task, $user);
    }
}
