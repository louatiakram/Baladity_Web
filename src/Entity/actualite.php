<?php

namespace App\Entity;

use App\Repository\ActualiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActualiteRepository::class)]
class actualite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_a;

    #[ORM\Column(type: 'string', length: 255)]
    private $description_a;

    #[ORM\Column(type: 'string', length: 255)]
    private $image_a;

    #[ORM\Column(type: 'date')]
    private $date_a;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre_a;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    public function getIdA(): ?int
    {
        return $this->id_a;
    }

    public function setIdA(int $id_a): self
    {
        $this->id_a = $id_a;

        return $this;
    }

    public function getDescriptionA(): ?string
    {
        return $this->description_a;
    }

    public function setDescriptionA(string $description_a): self
    {
        $this->description_a = $description_a;

        return $this;
    }

    public function getImageA(): ?string
    {
        return $this->image_a;
    }

    public function setImageA(string $image_a): self
    {
        $this->image_a = $image_a;

        return $this;
    }

    public function getDateA(): ?\DateTimeInterface
    {
        return $this->date_a;
    }

    public function setDateA(\DateTimeInterface $date_a): self
    {
        $this->date_a = $date_a;

        return $this;
    }

    public function getTitreA(): ?string
    {
        return $this->titre_a;
    }

    public function setTitreA(string $titre_a): self
    {
        $this->titre_a = $titre_a;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }
}
