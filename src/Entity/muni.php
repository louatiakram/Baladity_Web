<?php

namespace App\Entity;

use App\Repository\MunicipalityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
class muni
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_muni', type: 'integer')]
    private $id_muni;

    #[ORM\Column(name: 'nom_muni', type: 'string', length: 255)]
    private $nom_muni;

    #[ORM\Column(name: 'email_muni', type: 'string', length: 255)]
    private $email_muni;

    #[ORM\Column(name: 'password_muni', type: 'string', length: 255)]
    private $password_muni;

    #[ORM\Column(type: 'string', length: 255)]
    private $imagee_user;

    public function getIdMuni(): ?int
    {
        return $this->id_muni;
    }

    public function getNomMuni(): ?string
    {
        return $this->nom_muni;
    }

    public function setNomMuni(string $nomMuni): self
    {
        $this->nom_muni = $nomMuni;
        return $this;
    }

    public function getEmailMuni(): ?string
    {
        return $this->email_muni;
    }

    public function setEmailMuni(string $emailMuni): self
    {
        $this->email_muni = $emailMuni;
        return $this;
    }

    public function getPasswordMuni(): ?string
    {
        return $this->password_muni;
    }

    public function setPasswordMuni(string $passwordMuni): self
    {
        $this->password_muni = $passwordMuni;
        return $this;
    }

    public function getImageeuser(): ?string
    {
        return $this->imagee_user;
    }

    public function setImageeuser(string $imagee_user): self
    {
        $this->imagee_user = $imagee_user;
        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getNomMuni();
    }
}
