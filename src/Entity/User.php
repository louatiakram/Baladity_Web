<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
#[UniqueEntity(fields: ['id'], message: 'There is already an account with this id')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $type_user;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom_user;

    #[ORM\Column(type: 'string', length: 255)]
    private $email_user;

    #[ORM\Column(name: 'phoneNumber_user', type: 'string', length: 255)]
    private $phoneNumber_user;

    #[ORM\ManyToOne(targetEntity: muni::class)]
    #[ORM\JoinColumn(name: 'id_muni', referencedColumnName: 'id_muni')]
    private $id_muni;

    #[ORM\Column(type: 'string', length: 255)]
    private $location_user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image_user;

    #[ORM\Column(name: 'isBanned', type: 'boolean', nullable: true)]
    private $isBanned;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'Citoyen';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getTypeUser(): ?string
    {
        return $this->type_user;
    }
    
    public function setTypeUser(string $type_user): self
    {
        $this->type_user = $type_user;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): self
    {
        $this->nom_user = $nom_user;
        return $this;
    }

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser(string $email_user): self
    {
        $this->email_user = $email_user;
        return $this;
    }

    public function getPhoneNumberUser(): ?string
    {
        return $this->phoneNumber_user;
    }

    public function setPhoneNumberUser(string $phoneNumber_user): self
    {
        $this->phoneNumber_user = $phoneNumber_user;
        return $this;
    }

    public function getIdMuni(): ?muni
    {
        return $this->id_muni;
    }

    public function setIdMuni(?muni $id_muni): self
    {
        $this->id_muni = $id_muni;
        return $this;
    }

    public function getLocationUser(): ?string
    {
        return $this->location_user;
    }

    public function setLocationUser(string $location_user): self
    {
        $this->location_user = $location_user;
        return $this;
    }

    public function getImageUser(): ?string
    {
        return $this->image_user;
    }

    public function setImageUser(?string $image_user): self
    {
        $this->image_user = $image_user;
        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(?bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
