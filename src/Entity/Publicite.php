<?php

namespace App\Entity;

use App\Repository\PubliciteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PubliciteRepository::class)]
class publicite
{
   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_pub;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez ajouter un titre.")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: "Le titre de la publicité ne peut pas contenir de chiffres."
    )]
    private $titre_pub;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez ajouter une description.")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
      //  message: "La description de la publicité ne peut pas contenir de chiffres."
    )]
    #[Assert\Length(
        min: 5,
        minMessage: "La description de la publicité doit contenir au moins {{ limit }} caractères."
    )]
    private $description_pub;
    #[ORM\Column(type: 'integer')]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: "Le numéro de contact doit contenir exactement 8 chiffres."
    )]
    private $contact_pub;

    #[ORM\Column(type: 'string', length: 255)]
    private $localisation_pub;

    #[ORM\ManyToOne(targetEntity: actualite::class)]
    #[ORM\JoinColumn(name: 'id_a', referencedColumnName: 'id_a')]
    private $id_a;
   
 

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    
    private $image_pub;

    #[ORM\Column(type: 'string', length: 255)]
    private $offre_pub;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPub(): ?int
    {
        return $this->id_pub;
    }

    public function setIdPub(int $id_pub): self
    {
        $this->id_pub = $id_pub;

        return $this;
    }

    public function getTitrePub(): ?string
    {
        return $this->titre_pub;
    }

    public function setTitrePub(string $titre_pub): self
    {
        $this->titre_pub = $titre_pub;

        return $this;
    }

    public function getDescriptionPub(): ?string
    {
        return $this->description_pub;
    }

    public function setDescriptionPub(string $description_pub): self
    {
        $this->description_pub = $description_pub;

        return $this;
    }

    public function getContactPub(): ?int
    {
        return $this->contact_pub;
    }

    public function setContactPub(int $contact_pub): self
    {
        $this->contact_pub = $contact_pub;

        return $this;
    }

    public function getLocalisationPub(): ?string
    {
        return $this->localisation_pub;
    }

    public function setLocalisationPub(string $localisation_pub): self
    {
        $this->localisation_pub = $localisation_pub;

        return $this;
    }

    public function getIdA(): ?int
    {
        return $this->id_a;
    }

    public function setIdA(?actualite $id_a): self
    {
        $this->id_a = $id_a;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?enduser $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    public function getImagePub(): ?string
    {
        return $this->image_pub;
    }

    public function setImagePub(string $image_pub): self
    {
        $this->image_pub = $image_pub;

        return $this;
    }

    public function getOffrePub(): ?string
    {
        return $this->offre_pub;
    }

    public function setOffrePub(string $offre_pub): self
    {
        $this->offre_pub = $offre_pub;

        return $this;
    }
    public function getActualite(): ?actualite
{
    return $this->id_a;
}

public function setActualite(?actualite $actualite): self
{
    $this->id_a = $actualite;
    return $this;
}

}
