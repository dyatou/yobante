<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *  @ApiResource(
 *      attributes={"acces_control"="is_granted('POST_EDIT', subject)"}
 *)
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isactive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="user")
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="user")
     */
    private $depot;

    public function __construct()
    {
        $this->compte = new ArrayCollection();
        $this->depot = new ArrayCollection();
        $this->isactive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
       return [$this->role->getLibelle()];
        
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getIsactive(): ?bool
    {
        return $this->isactive;
    }


    public function setIsactive(bool $isactive): self
    {
        $this->isactive = $isactive;

        return $this;
    }
    public function isAccountNonExpired(){
        return true;
    }
     public function isAccountNonLocked(){
         return true;
     }
     public function isCredentialsNonExpired()
     {
         return true;
     }
     public function isEnabled(){
         return $this->isactive;
     }

     public function getPartenaire(): ?Partenaire
     {
         return $this->partenaire;
     }

     public function setPartenaire(?Partenaire $partenaire): self
     {
         $this->partenaire = $partenaire;

         return $this;
     }

     /**
      * @return Collection|Compte[]
      */
     public function getCompte(): Collection
     {
         return $this->compte;
     }

     public function addCompte(Compte $compte): self
     {
         if (!$this->compte->contains($compte)) {
             $this->compte[] = $compte;
             $compte->setUser($this);
         }

         return $this;
     }

     public function removeCompte(Compte $compte): self
     {
         if ($this->compte->contains($compte)) {
             $this->compte->removeElement($compte);
             // set the owning side to null (unless already changed)
             if ($compte->getUser() === $this) {
                 $compte->setUser(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Depot[]
      */
     public function getDepot(): Collection
     {
         return $this->depot;
     }

     public function addDepot(Depot $depot): self
     {
         if (!$this->depot->contains($depot)) {
             $this->depot[] = $depot;
             $depot->setUser($this);
         }

         return $this;
     }

     public function removeDepot(Depot $depot): self
     {
         if ($this->depot->contains($depot)) {
             $this->depot->removeElement($depot);
             // set the owning side to null (unless already changed)
             if ($depot->getUser() === $this) {
                 $depot->setUser(null);
             }
         }

         return $this;
     }
    
}

