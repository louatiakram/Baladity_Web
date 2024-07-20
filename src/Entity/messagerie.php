<?php

namespace App\Entity;

use App\Repository\MessagerieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MessagerieRepository::class)]
class messagerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_message;

    #[ORM\Column(type: 'datetime')]
    private $date_message;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le contenu du message ne peut pas être vide.")]
    private $contenu_message;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'receiverId_message', referencedColumnName: 'id_user')]
    private $receiverId_message;

    #[ORM\ManyToOne(targetEntity: enduser::class)]
    #[ORM\JoinColumn(name: 'senderId_message', referencedColumnName: 'id_user')]
    private $senderId_message;

    #[ORM\Column(type: 'string', length: 255)]
    private $type_message;

    public function getIdMessage(): ?int
    {
        return $this->id_message;
    }

    public function setIdMessage(int $id_message): self
    {
        $this->id_message = $id_message;

        return $this;
    }

    public function getDateMessage(): ?\DateTimeInterface
    {
        return $this->date_message;
    }

    public function setDateMessage(\DateTimeInterface $date_message): self
    {
        $this->date_message = $date_message;

        return $this;
    }

    public function getContenuMessage(): ?string
    {
        return $this->contenu_message;
    }

    public function setContenuMessage(string $contenu_message): self
    {
        $this->contenu_message = $contenu_message;

        return $this;
    }

    public function getReceiverIdMessage(): ?EndUser
    {
        return $this->receiverId_message;
    }

    public function setReceiverIdMessage(EndUser $receiverId_message): self
    {
        $this->receiverId_message = $receiverId_message;

        return $this;
    }

    public function getSenderIdMessage(): ?EndUser
    {
        return $this->senderId_message;
    }

    public function setSenderIdMessage(?Enduser $senderIdMessage): self
    {
        $this->senderId_message = $senderIdMessage;
    
        return $this;
    }

    public function getTypeMessage(): ?string
    {
        return $this->type_message;
    }

    public function setTypeMessage(string $type_message): self
    {
        $this->type_message = $type_message;

        return $this;
    }
}
