<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api") 
 */
class ComptePartenaireController extends AbstractController
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
/**
 * @Route("/new/compte", name="creation_compte_nouveauPartenaire", methods={"POST"})
 */
    public function nouveau_compte(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $PasswordEncoderInterface, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $userCreateur = $this->tokenStorage->getToken()->getUser();

        $values = json_decode($request->getContent());
        if(isset($values->ninea))
        {
     
        $dateCreation = new \DateTime();
          //var_dump($dateCreation);die;
          $depot = new Depot();
          $compte = new Compte();                     
          $user = new User();
          $partenaire = new Partenaire(); 

    //pour le compte USER  

          $roleRepository = $this->getDoctrine()->getRepository(Role::class);
          $role=$roleRepository->find($values->role);

    //partenaire
          $partenaire->setNinea($values->ninea);
          $partenaire->setRegistrecommerce($values->registrecommerce); 
                

          $entityManager->persist($partenaire);
           $entityManager->flush();

    //Information du user cretaeur

        $user->setNom($values->nom);
        $user->setPrenom($values->prenom);
         $user->setUsername($values->username);
        $user->setPassword($PasswordEncoderInterface->encodePassword($user, $values->password));
        $user->setRole($role);
        $user->setPartenaire($partenaire);

                
                $entityManager->persist($user);
                $entityManager->flush();

                

        //GENERER COMPTE  
                $annee = Date('y');
                $nb = $this->getLastCompte();
                $long = strlen($nb);
                $nin = substr($partenaire->getNinea() , -2);
                $NumCompte = str_pad("NG".$annee.$nin, 11-$long, "0").$nb;

          
    // recuperation  de l'utilisateur createur du compte et faire  un depot initial

             $userCreateur = $this->tokenStorage->getToken()->getUser();
             $compte->setNumCompte($NumCompte);
            
             $compte->setSolde(0);
             $compte->setDatecreation($dateCreation);
             $compte->setUser($userCreateur);
             $compte->setPartenaire($partenaire); 
              
             $entityManager->persist($compte);
             //var_dump($Compte);die;
             $entityManager->flush();

             //pour le depot

             $depot->setDateDepot($dateCreation);
             $depot->setMontant($values->montant);
             $depot->setUser($userCreateur);
             $depot->setCompte($compte);

             $entityManager->persist($depot);
             $entityManager->flush(); 


     // mis a joure le solde du compte partenaire 
              $NewSolde = ($values->montant+$compte->getSolde());
              $compte->setSolde($NewSolde);

              $entityManager->persist($compte);
              $entityManager->flush();
              
         $data = [
        'status' => 201,
        'message' => 'Le compte partenaire est bien cree avec un depot de:'.$values->montant
           ];
         return new JsonResponse($data, 201);
        }

        $data = [
           'status' => 500,
            'message' => 'veuillez renseignerles fialiations du partenaire'
             ];
            return new JsonResponse($data, 500);
         
                       

                       }
/**
 * @Route("/compteExistent", name="creation_compte_PartenaireExistent", methods={"POST"})
 */
public function creation_compte_PartenaireExistent(Request $request, EntityManagerInterface $entityManager)
{
    $values = json_decode($request->getContent());
        if(isset($values->ninea))
{
    
        // $this->denyAccessUnlessGranted('POST_EDIT',$this->getUser());

        $ReposPropicompte = $this->getDoctrine()->getRepository(Partenaire::class);

                 // recuperer le proprietaire du compte

        $propicompte = $ReposPropicompte->findOneBy($values->ninea);

        if ($propicompte) 

        {
                                 
        $dateJours = new \DateTime();
        $depot = new Depot();
        $compte = new Compte();

          //pour le compte 
                                
  // recuperer de l'utilisateur createur du compte et faire un depot initial
      $userCreateur = $this->tokenStorage->getToken()->getUser();

        //generation du comptes
         $annee = Date('y');
            $yb = $this->getLastCompte();
            $long = strlen($yb);
            $nin = substr($propicompte->getNinea() , -2);
            $Numcompte = str_pad("NG".$annee.$nin, 11-$long, "0").$yb;
                                
            $compte->setNumcompte($Numcompte);
            $compte->setSolde(0);
            $compte->setDatecreation($dateJours);
            $compte->setUser($userCreateur);
            $compte->setPartenaire($propicompte);

            $entityManager->persist($compte);
            $entityManager->flush();

        //depot

        $ReposCompte = $this->getDoctrine()->getRepository(Compte::class);
        $compteDepot = $ReposCompte->find($Numcompte);
        $depot->setDateDepot($dateJours);
        $depot->setMontant($values->montant);
        $depot->setUser($userCreateur);
        $depot->setCompte($compteDepot);
                                    

            $entityManager->persist($depot);
         $entityManager->flush();

    // mis a joure le solde compte partenaire 

     $NewSolde = ($values->mtt+$compte->getSolde());
                                        
     $compte->setSolde($NewSolde);
                
    $entityManager->persist($compte);
            $entityManager->flush();

        $data = [
             'status' => 201,
             'message' => 'Le compte du partenaire est bien cree avec un depot initia de: '.$values->mtt
                 ];
                return new JsonResponse($data, 201);
                                
         $data = [
          'status' => 500,
             'message' => 'Veuillez saisir un montant de depot valide'
                 ];
             return new JsonResponse($data, 500);
             }
        $data = [
            'status' => 500,
            'message' => 'Desole le NINEA n existe psa' 
            ];
        return new JsonResponse($data, 500);
         }
        $data = [
        'status' => 500,
            'message' => 'veuillez reneigner les filiations du partenaire'
        ];
            return new JsonResponse($data, 500);
        }    

 public function getLastCompte()
 {
    $ripo = $this->getDoctrine()->getRepository(Compte::class);

     $compte = $ripo->findBy([], ['id'=>'DESC']);

          if(!$compte)
        {
            $c = 1;
        } 
        else

        {
            $c= ($compte[0]->getId()+1);

             }
             return $c;
}
    
 //faire depot
/**
 * @Route("/faire/depot", name="fairre depot", methods={"POST"})
 */

public function faireDepot(Request $request, EntityManagerInterface $entityManager)

{   
    $userCreateur = $this->tokenStorage->getToken()->getUser();

    $values = json_decode($request->getContent());

         if($values->montant>0)

   {
            
        $dateJours = new \DateTime();
        $depot = new Depot();

        $ReposCompte = $this->getDoctrine()->getRepository(Compte::class);
            $compteDepot = $ReposCompte->findOneBy(array($values->id));
            $depot->setDateDepot($dateJours);
            $depot->setMontant($values->montant);
            $depot->setUser($userCreateur);
            $depot->setCompte($compteDepot);
                                    
             $entityManager->persist($depot);
            $entityManager->flush();

     // mis a joure le solde du compte partenaire 
            $NewSolde = ($values->montant+$compteDepot->getSolde());
            $compteDepot->setSolde($NewSolde);

            $entityManager->persist($compteDepot);
            $entityManager->flush();
                            
                $data = [
                    'status' => 201,
                         'message' => 'Merci vous avez fait un depot de:'.$values->montant
                    ];
                return new JsonResponse($data, 201);
         }

         $data = [
             'status' => 500,
        'message' => 'renseignez les filiations du partenaire,ainsi que le montant a deposer'
            ];
        return new JsonResponse($data, 500);
    }
}
        
    