<?php

namespace App\Entity;

use App\Repository\CommentaireTacheRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireTacheRepository::class)]
class commentairetache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_C', type: 'integer')]
    private ?int $id_C;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private ?enduser $id_user;

    #[ORM\ManyToOne(targetEntity: tache::class, inversedBy: 'commentaireTache')]
    #[ORM\JoinColumn(name: 'id_T', referencedColumnName: 'id_T', onDelete: 'CASCADE')]
    private ?tache $tache;

    #[ORM\Column(name: 'date_C', type: 'date')]
    private ?DateTimeInterface $date_C;

    #[ORM\Column(name: 'texte_C', type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournir un commentaire.')]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'Le commentaire doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le commentaire ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]+$/',
        message: 'Le commentaire ne peut contenir que des lettres, des chiffres et des espaces.'
    )]
    private ?string $texte_C;

    public function getIdC(): ?int
    {
        return $this->id_C;
    }

    public function getDateC(): ?DateTimeInterface
    {
        return $this->date_C;
    }

    public function setDateC(?DateTimeInterface $dateC): self
    {
        $this->date_C = $dateC;
        return $this;
    }

    public function getTexteC(): ?string
    {
        return $this->texte_C;
    }

    public function setTexteC(?string $texteC): self
    {
        $this->texte_C = $texteC;
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

    public function getIdT(): ?tache
    {
        return $this->tache;
    }

    public function setIdT(?tache $tache): self
    {
        $this->tache = $tache;
        return $this;
    }
}