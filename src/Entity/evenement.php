<?php

namespace App\Entity;

use DateTimeInterface;
use App\Entity\enduser;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_E;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user')]
    #[ORM\Column(type: 'integer')]
    private $id_user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournir nom.')]
    #[Assert\Type(type: 'string', message: 'Le nom doit être une chaîne de caractères.')]
    private $nom_E;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez fournir date debut.')]
    #[Assert\LessThan(propertyPath: "date_DHF", message: "La date de début doit être avant la date de fin.")]
    private ?DateTimeInterface $date_DHE;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez fournir date fin.')]
    #[Assert\GreaterThan(propertyPath: "date_DHE", message: "La date de fin doit être après la date de début.")]
    private ?DateTimeInterface $date_DHF;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Veuillez fournir capacite.')]
    #[Assert\Type(type: 'integer', message: 'La capacité doit être un entier.')]
    private $capacite_E;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez fournir categorie.')]
    private $categorie_E;

    #[ORM\Column(name: 'imageEvent',type: 'string', length: 255)]
    private $imageEvent;

    private $enduser;
    private $attendees;

   public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    /**
     * @return Collection|enduser[]
     */
    public function getAttendees(): Collection
{
    if ($this->attendees === null) {
        $this->attendees = new ArrayCollection();
    }
    return $this->attendees;
}

    public function addAttendee(enduser $enduser): self
    {
        if (!$this->attendees->contains($enduser)) {
            $this->attendees[] = $enduser;
            $enduser->setEvent($this);
        }

        return $this;
    }

    public function removeAttendee(enduser $enduser): self
    {
        if ($this->attendees->removeElement($enduser)) {
            // set the owning side to null (unless already changed)
            if ($enduser->getEvent() === $this) {
                $enduser->setEvent(null);
            }
        }

        return $this;
    }

    public function getEnduser(): ?enduser
    {
        return $this->enduser;
    }

    public function setEnduser(?enduser $enduser): self
    {
        $this->enduser = $enduser;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id_E;
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

    public function getNomE(): ?string
    {
        return $this->nom_E;
    }

    public function setNomE(?string $nom_E): self
    {
        $this->nom_E = $nom_E;

        return $this;
    }

    public function getDateDHE(): ?\DateTimeInterface
    {
        return $this->date_DHE;
    }

    public function setDateDHE(?\DateTimeInterface $date_DHE): self
    {
        $this->date_DHE = $date_DHE;

        return $this;
    }

    public function getDateDHF(): ?\DateTimeInterface
    {
        return $this->date_DHF;
    }

    public function setDateDHF(?\DateTimeInterface $date_DHF): self
    {
        $this->date_DHF = $date_DHF;

        return $this;
    }

    public function getCapaciteE(): ?int
    {
        return $this->capacite_E;
    }

    public function setCapaciteE(?int $capacite_E): self
    {
        $this->capacite_E = $capacite_E;

        return $this;
    }

    public function getCategorieE(): ?string
    {
        return $this->categorie_E;
    }

    public function setCategorieE(?string $categorie_E): self
    {
        $this->categorie_E = $categorie_E;

        return $this;
    }

    public function getImageEvent(): ?string
    {
        return $this->imageEvent;
    }

    public function setImageEvent(?string $imageEvent): self
    {
        $this->imageEvent = $imageEvent;

        return $this;
    }
}