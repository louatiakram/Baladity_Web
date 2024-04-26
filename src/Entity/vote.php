<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_V;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\Column(type: 'integer')]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournit une description.')]
    #[Assert\Type(type: 'string', message: 'Description must be a string.')]
    private $desc_E;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez fournir date soummission.')]
    private $date_SV;

    public function getIdV(): ?int
    {
        return $this->id_V;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(?int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getDescE(): ?string
    {
        return $this->desc_E;
    }

    public function setDescE(?string $desc_E): self
    {
        $this->desc_E = $desc_E;

        return $this;
    }

    public function getDateSV(): ?\DateTimeInterface
    {
        return $this->date_SV;
    }

    public function setDateSV(?\DateTimeInterface $date_SV): self
    {
        $this->date_SV = $date_SV;

        return $this;
    }

    
}
