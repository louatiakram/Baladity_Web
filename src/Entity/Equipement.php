<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Avis;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_equipement', type: 'integer')]
    private $id_equipement;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $reference_eq;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez ajouter le nom.")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: "Le nom de l'équipement ne peut pas contenir de chiffres."
    )]
    private $nom_eq;

    #[ORM\Column(type: 'string', length: 255)]
    private $categorie_eq;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive(message: "la quantité doit etre positive.")]
    private $quantite_eq;

    #[ORM\Column(type: 'datetime')]
    private $date_ajouteq;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez ajouter une description.")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]+$/',
        message: 'La description ne peut contenir que des lettres, des chiffres et des espaces.'
    )]
    private $description_eq;

    #[ORM\Column(type: 'string', length: 255)]
    private $image_eq;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    #[ORM\ManyToOne(targetEntity: muni::class)]
    #[ORM\JoinColumn(name: 'id_muni', referencedColumnName: 'id_muni')]
    private $id_muni;

    //#[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'id_equipement', cascade: ['remove'])]
    //private $avis_eq;

    public function __construct()
    {
        $this->avis_eq = new ArrayCollection();
    }

    public function getIdEquipement(): ?int
    {
        return $this->id_equipement;
    }

    public function getReferenceEq(): ?string
    {
        return $this->reference_eq;
    }

    public function setReferenceEq(?string $reference_eq): self
    {
        $this->reference_eq = $reference_eq;

        return $this;
    }

    public function getNomEq(): ?string
    {
        return $this->nom_eq;
    }

    public function setNomEq(string $nom_eq): self
    {
        $this->nom_eq = $nom_eq;

        return $this;
    }

    public function getCategorieEq(): ?string
    {
        return $this->categorie_eq;
    }

    public function setCategorieEq(string $categorie_eq): self
    {
        $this->categorie_eq = $categorie_eq;

        return $this;
    }

    public function getQuantiteEq(): ?int
    {
        return $this->quantite_eq;
    }

    public function setQuantiteEq(int $quantite_eq): self
    {
        $this->quantite_eq = $quantite_eq;

        return $this;
    }

    public function getDateAjouteq(): ?\DateTimeInterface
    {
        return $this->date_ajouteq;
    }

    public function setDateAjouteq(\DateTimeInterface $date_ajouteq): self
    {
        $this->date_ajouteq = $date_ajouteq;

        return $this;
    }

    public function getDescriptionEq(): ?string
    {
        return $this->description_eq;
    }

    public function setDescriptionEq(string $description_eq): self
    {
        $this->description_eq = $description_eq;

        return $this;
    }

    public function getImageEq(): ?string
    {
        return $this->image_eq;
    }

    public function setImageEq(string $image_eq): self
    {
        $this->image_eq = $image_eq;

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

    public function getIdMuni(): ?muni
    {
        return $this->id_muni;
    }

    public function setIdMuni(?muni $id_muni): self
    {
        $this->id_muni = $id_muni;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisEq(): Collection
    {
        return $this->avis_eq;
    }

    public function addAvisEq(Avis $avisEq): self
    {
        if (!$this->avis_eq->contains($avisEq)) {
            $this->avis_eq[] = $avisEq;
            $avisEq->setIdEquipement($this);
        }

        return $this;
    }

    public function removeAvisEq(Avis $avisEq): self
    {
        if ($this->avis_eq->removeElement($avisEq)) {
            // set the owning side to null (unless already changed)
            if ($avisEq->getIdEquipement() === $this) {
                $avisEq->setIdEquipement(null);
            }
        }

        return $this;
    }
}
