<?php
namespace App\Security;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if ($user->isValid() == false){
            throw new DisabledException('Account disabled');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
        return;
    }

    // user account is expired, the user may be notified
    if ($user->isExpired()) {
    throw new AccountExpiredException('...');
    }
    }
}