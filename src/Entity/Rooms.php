<?php

namespace App\Entity;

use App\Repository\RoomsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomsRepository::class)]
class Rooms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?comptes $hostId = null;

    #[ORM\Column(length: 255)]
    private ?string $roomId = null;

    #[ORM\Column]
    private ?int $playerNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roomPassword = null;

    #[ORM\Column]
    private ?bool $publicRoom = null;

    #[ORM\Column]
    private ?bool $started = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHostId(): ?comptes
    {
        return $this->hostId;
    }

    public function setHostId(?comptes $hostId): static
    {
        $this->hostId = $hostId;

        return $this;
    }

    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    public function setRoomId(string $roomId): static
    {
        $this->roomId = $roomId;

        return $this;
    }

    public function getPlayerNumber(): ?int
    {
        return $this->playerNumber;
    }

    public function setPlayerNumber(int $playerNumber): static
    {
        $this->playerNumber = $playerNumber;

        return $this;
    }

    public function getRoomPassword(): ?string
    {
        return $this->roomPassword;
    }

    public function setRoomPassword(?string $roomPassword): static
    {
        $this->roomPassword = $roomPassword;

        return $this;
    }

    public function isPublicRoom(): ?bool
    {
        return $this->publicRoom;
    }

    public function setPublicRoom(bool $publicRoom): static
    {
        $this->publicRoom = $publicRoom;

        return $this;
    }

    public function isStarted(): ?bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): static
    {
        $this->started = $started;

        return $this;
    }
}
