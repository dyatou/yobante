<?php

namespace App\Security\Voter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        return in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) 
        {
            return false;
        }

        if ($user->getRoles()[0] === 'ROLE_SUPADMIN' && $subject->getRoles()[0] != 'ROLE_SUPADMIN')
        {
            return true;
        }

        //if ($user->getRoles()[0] === 'ROLE_CAISSIER' || $user->getRoles()[0] === 'ROLE_PARTENAIRE')
        //{
            //return false;
       // }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'POST_EDIT':
                if ( $user->getRoles()[0] === 'ROLE_ADMIN' && 
                ($subject->getRole()->getLibelle() === 'ROLE_CAISSIER' ||
                $subject->getRole()->getLibelle() === 'ROLE_PARTENAIRE')){ 
                return true;
                }
                else if($user->getRoles()[0]==="ROLE_CAISSIER"){
                    return false;
                }
                break;
            case 'POST_VIEW':
                if($user->getRoles()[0]==="ROL_CAISSIER"){
                    return false;
                }
                // logic to determine if the user can VIEW
                // return true or false
                break;
             default;
                break;   
        }

        return false;
    }
}
