<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use App\Entity\Equipement;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_avis;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private $note_avis;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $commentaire_avis;

    #[ORM\Column(type: 'datetime')]
    private $date_avis;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    #[ORM\ManyToOne(targetEntity: muni::class)]
    #[ORM\JoinColumn(name: 'id_muni', referencedColumnName: 'id_muni')]
    private $id_muni;

    #[ORM\ManyToOne(targetEntity: equipement::class, inversedBy: 'avis_eq')]
    #[ORM\JoinColumn(name: 'id_equipement', referencedColumnName: 'id_equipement')]
    private $equipement;

    public function getIdAvis(): ?int
    {
        return $this->id_avis;
    }

    public function getNoteAvis(): ?int
    {
        return $this->note_avis;
    }

    public function setNoteAvis(int $note_avis): self
    {
        $this->note_avis = $note_avis;

        return $this;
    }

    public function getCommentaireAvis(): ?string
    {
        return $this->commentaire_avis;
    }

    public function setCommentaireAvis(string $commentaire_avis): self
    {
        $this->commentaire_avis = $commentaire_avis;

        return $this;
    }

    public function getDateAvis(): ?\DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDateAvis(\DateTimeInterface $date_avis): self
    {
        $this->date_avis = $date_avis;

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

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }
    
    public function setEquipement(?Equipement $equipement): self
    {
        $this->equipement = $equipement;
    
        return $this;
    }
    public function getIdEquipement(): ?Equipement
    {
        return $this->id_equipement;
    }
    public function setIdEquipement(?Equipement $id_equipement): self
    {
        $this->id_equipement = $id_equipement;
    
        return $this;
    }
}
