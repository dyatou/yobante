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
        $role->setLibelle("ROLE_SUPADMIN");
        $manager->persist($role);

        
        $role1 = new Role();
        $role1->setLibelle("ROLE_ADMIN");
        $manager->persist($role1);

        $role2 = new Role();
        $role2->setLibelle("ROLE_CAISSIER");
        $manager->persist($role2);

        $role3 = new Role();
        $role3->setLibelle("ROLE_PARTENAIRE");
        $manager->persist($role3);



        
        $user = new User();
        $user->setUsername("khady");
        $user->setRole($role);
        $user->setPassword($this->encoder->encodePassword($user, "supadmin"));
        $user->setRoles(json_encode(array($role->getLibelle())));
        $user->setPrenom("dyatou");
        $user->setNom("sarr");
        $user->setIsactive(true);

        $manager->persist($user);

        $manager->flush();
    }
}
