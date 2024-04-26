<?php

namespace App\Entity;

use App\Repository\EndUserRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: EndUserRepository::class)]
class enduser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom_user;

    #[ORM\Column(type: 'string', length: 255)]
    private $email_user;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $type_user;

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

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }
    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

   public function setEvent(evenement $event): self
{
    $this->event = $event;
    return $this;
}
}
