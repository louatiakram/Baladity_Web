<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: TacheRepository::class)]
class tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_T', type: 'integer')]
    private $id_T;

    #[ORM\Column(name: 'nom_Cat', type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournir categorie.')]
    private $nom_Cat;

    
    #[ORM\Column(name: 'titre_T', type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournir un titre.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]*$/',
        message: 'Le titre ne doit contenir que des lettres et des espaces.'
    )]
    #[Assert\Length(
        min: 4,
        max: 30,
        minMessage: 'Le titre doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private $titre_T;

    #[ORM\Column(name: 'pieceJointe_T', type: 'string', length: 255)]
    private $pieceJointe_T;

    #[ORM\Column(name: 'date_DT', type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez fournir une date Debut.')]
    private $date_DT;

    #[ORM\Column(name: 'date_FT', type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez fournir une date Fin.')]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "date_DT",
        message: "La date de fin doit être postérieure ou égale à la date de début."
    )]
    private $date_FT;

    #[ORM\Column(name: 'desc_T', type: 'string', length: 255)]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9\s]+$/',
        message: 'La description ne peut contenir que des lettres, des chiffres et des espaces.'
    )]
    private $desc_T;

    #[ORM\Column(name: 'etat_T', type: 'string', columnDefinition: "ENUM('TODO', 'DOING', 'DONE')")]
    #[Assert\NotBlank(message: 'Veuillez fournir un Etat.')]
    private $etat_T;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    private $id_user;

    #[ORM\OneToOne(targetEntity: commentairetache::class, mappedBy: 'id_T')]
    private $commentaireTache;


    public function getCommentaireTache(): ?commentairetache
    {
        return $this->commentaireTache;
    }
    public function getIdT(): ?int
    {
        return $this->id_T;
    }

    public function getNomCat(): ?string
    {
        return $this->nom_Cat;
    }

    public function setNomCat(?string $nomCat): self
    {
        $this->nom_Cat = $nomCat;
        return $this;
    }

    public function getTitreT(): ?string
    {
        return $this->titre_T;
    }

    public function setTitreT(?string $titreT): self
    {
        $this->titre_T = $titreT;
        return $this;
    }

    public function getPieceJointeT(): ?string
    {
        return $this->pieceJointe_T;
    }

    public function setPieceJointeT(?string $pieceJointeT): self
    {
        $this->pieceJointe_T = $pieceJointeT;
        return $this;
    }

    public function getDateDT(): ?\DateTimeInterface
    {
        return $this->date_DT;
    }

    public function setDateDT(?\DateTimeInterface $dateDT): self
    {
        $this->date_DT = $dateDT;
        return $this;
    }

    public function getDateFT(): ?\DateTimeInterface
    {
        return $this->date_FT;
    }

    public function setDateFT(?\DateTimeInterface $dateFT): self
    {
        $this->date_FT = $dateFT;
        return $this;
    }

    public function getDescT(): ?string
    {
        return $this->desc_T;
    }

    public function setDescT(?string $descT): self
    {
        $this->desc_T = $descT;
        return $this;
    }

    public function getEtatT(): ?string
    {
        return $this->etat_T;
    }

    public function setEtatT(?string $etatT): self
    {
        $this->etat_T = $etatT;
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
}