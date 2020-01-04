<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    
    public function load(ObjectManager $manager)
    {  
        $role = new Role();
        $role->setLibelle("supadmin");


        $manager->persist($role);

        $manager->flush();
        
        $user = new User("supadmin");
        $user->setUsername("khady");
        $user->setRole($role);
        $user->setPassword($this->encoder->encodePassword($user, "supadmin"));
        $user->setRoles(json_encode(array("ROLE_SUPADMIN")));
        $user->setPrenom("khady");
        $user->setNom("sarr");
        $user->setIsactive(true);

        $manager->persist($user);

        $manager->flush();
    }
}
