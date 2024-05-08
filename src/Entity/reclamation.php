<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class reclamation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_reclamation;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez mettre le sujet de votre réclamation.")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: "Le sujet de la réclamation ne peut pas contenir de chiffres."
    )]
    private $sujet_reclamation;

    #[ORM\Column(type: 'date')]
    private $date_reclamation;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez choisir le type de votre réclamation.")]
    private $type_reclamation;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez mettre la description de votre réclamation.")]
    private $description_reclamation;

    #[ORM\Column(type: 'string', length: 255)]
    private $status_reclamation;

    #[ORM\Column(type: 'string', length: 255)]
    private $image_reclamation;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez mettre l'adresse de votre réclamation.")]
    private $adresse_reclamation;

    #[ORM\ManyToOne(targetEntity: muni::class)]
    #[ORM\JoinColumn(name: 'id_muni', referencedColumnName: 'id_muni')]
    private $id_muni;

    public function getIdReclamation(): ?int
    {
        return $this->id_reclamation;
    }

    public function setIdReclamation(int $id_reclamation): self
    {
        $this->id_reclamation = $id_reclamation;

        return $this;
    }

    public function getIdUser(): ?enduser
    {
        return $this->id_user;
    }

    public function setIdUser(?enduser $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getSujetReclamation(): ?string
    {
        return $this->sujet_reclamation;
    }

    public function setSujetReclamation(string $sujet_reclamation): self
    {
        $this->sujet_reclamation = $sujet_reclamation;

        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->date_reclamation;
    }

    public function setDateReclamation(\DateTimeInterface $date_reclamation): self
    {
        $this->date_reclamation = $date_reclamation;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->type_reclamation;
    }

    public function setTypeReclamation(string $type_reclamation): self
    {
        $this->type_reclamation = $type_reclamation;

        return $this;
    }

    public function getDescriptionReclamation(): ?string
    {
        return $this->description_reclamation;
    }

    public function setDescriptionReclamation(string $description_reclamation): self
    {
        $this->description_reclamation = $description_reclamation;

        return $this;
    }

    public function getStatusReclamation(): ?string
    {
        return $this->status_reclamation;
    }

    public function setStatusReclamation(string $status_reclamation): self
    {
        $this->status_reclamation = $status_reclamation;

        return $this;
    }

    public function getImageReclamation(): ?string
    {
        return $this->image_reclamation;
    }

    public function setImageReclamation(string $image_reclamation): self
    {
        $this->image_reclamation = $image_reclamation;

        return $this;
    }

    public function getAdresseReclamation(): ?string
    {
        return $this->adresse_reclamation;
    }

    public function setAdresseReclamation(string $adresse_reclamation): self
    {
        $this->adresse_reclamation = $adresse_reclamation;

        return $this;
    }

    public function getIdMuni(): ?int
    {
        return $this->id_muni;
    }

    public function setIdMuni(?muni $id_muni): self
    {
        $this->id_muni = $id_muni;

        return $this;
    }
}
