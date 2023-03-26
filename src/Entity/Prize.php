<?php

namespace App\Entity;

use App\Repository\PrizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrizeRepository::class)]
#[ORM\Index(columns: ['partner_code'], name: 'columns_idx')]
class Prize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $partner_code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 150)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'prizes')]
    #[ORM\JoinColumn(nullable: false, columnDefinition: "INT NOT NULL AFTER `id`")]
    private ?Language $language = null;

    #[ORM\OneToMany(mappedBy: 'prize', targetEntity: UserGamePrize::class)]
    private Collection $userGamePrizes;

    #[ORM\Column]
    private ?bool $won = null;

    public function __construct()
    {
        $this->userGamePrizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartnerCode(): ?string
    {
        return $this->partner_code;
    }

    public function setPartnerCode(string $partner_code): self
    {
        $this->partner_code = $partner_code;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

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
            $userGamePrize->setPrize($this);
        }

        return $this;
    }

    public function removeUserGamePrize(UserGamePrize $userGamePrize): self
    {
        if ($this->userGamePrizes->removeElement($userGamePrize)) {
            // set the owning side to null (unless already changed)
            if ($userGamePrize->getPrize() === $this) {
                $userGamePrize->setPrize(null);
            }
        }

        return $this;
    }

    public function isWon(): ?bool
    {
        return $this->won;
    }

    public function setWon(bool $won): self
    {
        $this->won = $won;

        return $this;
    }
}
