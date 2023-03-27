<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: UserGamePrize::class)]
    private Collection $userGamePrizes;

    public function __construct()
    {
        $this->userGamePrizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return Collection<int, UserGamePrize>
     */
    public function getUserGamePrizes(): Collection
    {
        return $this->userGamePrizes;
    }

    public function addUserGamePrize(UserGamePrize $userGamePrize): self
    {
        if (!$this->userGamePrizes->contains($userGamePrize)) {
            $this->userGamePrizes->add($userGamePrize);
            $userGamePrize->setGame($this);
        }

        return $this;
    }

    public function removeUserGamePrize(UserGamePrize $userGamePrize): self
    {
        if ($this->userGamePrizes->removeElement($userGamePrize)) {
            // set the owning side to null (unless already changed)
            if ($userGamePrize->getGame() === $this) {
                $userGamePrize->setGame(null);
            }
        }

        return $this;
    }

    public function asArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'start_date' => $this->getStartDate()->format('Y-m-d'),
            'end_date' => $this->getEndDate()->format('Y-m-d'),
        ];
    }
}
