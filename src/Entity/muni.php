<?php

namespace App\Entity;

use App\Repository\MunicipalityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
class muni
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_muni', type: 'integer')]
    private $id_muni;

    #[ORM\Column(name: 'nom_muni', type: 'string', length: 255)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: "Le sujet de la réclamation ne peut pas contenir de chiffres."
    )]    
    private $nom_muni;

    #[ORM\Column(name: 'email_muni', type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
        message: "L'email n'est pas au format valide."
    )]
    private $email_muni;

    #[ORM\Column(name: 'password_muni', type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez mettre votre password.")]
    #[Assert\Regex(
        pattern: '/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{6,}$/',
        message: "Le mot de passe doit contenir au moins 6 caractères, dont au moins un chiffre et une lettre."
    )]
    private $password_muni;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez sélectionner une image.")]
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
