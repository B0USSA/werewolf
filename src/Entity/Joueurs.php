<?php

namespace App\Entity;

use App\Repository\JoueursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoueursRepository::class)]
class Joueurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, rooms>
     */
    #[ORM\ManyToMany(targetEntity: rooms::class, inversedBy: 'joueurs')]
    private Collection $roomId;

    /**
     * @var Collection<int, comptes>
     */
    #[ORM\ManyToMany(targetEntity: comptes::class, inversedBy: 'joueurs')]
    private Collection $compteId;

    #[ORM\Column]
    private ?bool $dead = null;

    #[ORM\Column]
    private ?bool $inRoom = null;

    public function __construct()
    {
        $this->roomId = new ArrayCollection();
        $this->compteId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, rooms>
     */
    public function getRoomId(): Collection
    {
        return $this->roomId;
    }

    public function addRoomId(rooms $roomId): static
    {
        if (!$this->roomId->contains($roomId)) {
            $this->roomId->add($roomId);
        }

        return $this;
    }

    public function removeRoomId(rooms $roomId): static
    {
        $this->roomId->removeElement($roomId);

        return $this;
    }

    /**
     * @return Collection<int, comptes>
     */
    public function getCompteId(): Collection
    {
        return $this->compteId;
    }

    public function addCompteId(comptes $compteId): static
    {
        if (!$this->compteId->contains($compteId)) {
            $this->compteId->add($compteId);
        }

        return $this;
    }

    public function removeCompteId(comptes $compteId): static
    {
        $this->compteId->removeElement($compteId);

        return $this;
    }

    public function isDead(): ?bool
    {
        return $this->dead;
    }

    public function setDead(bool $dead): static
    {
        $this->dead = $dead;

        return $this;
    }

    public function isInRoom(): ?bool
    {
        return $this->inRoom;
    }

    public function setInRoom(bool $inRoom): static
    {
        $this->inRoom = $inRoom;

        return $this;
    }
}
