<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'language', targetEntity: Partner::class)]
    private Collection $partners;

    #[ORM\OneToMany(mappedBy: 'language', targetEntity: Prize::class)]
    private Collection $prizes;

    public function __construct()
    {
        $this->partners = new ArrayCollection();
        $this->prizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Partner>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): self
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setLanguage($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): self
    {
        if ($this->partners->removeElement($partner)) {
            // set the owning side to null (unless already changed)
            if ($partner->getLanguage() === $this) {
                $partner->setLanguage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prize>
     */
    public function getPrizes(): Collection
    {
        return $this->prizes;
    }

    public function addPrize(Prize $prize): self
    {
        if (!$this->prizes->contains($prize)) {
            $this->prizes->add($prize);
            $prize->setLanguage($this);
        }

        return $this;
    }

    public function removePrize(Prize $prize): self
    {
        if ($this->prizes->removeElement($prize)) {
            // set the owning side to null (unless already changed)
            if ($prize->getLanguage() === $this) {
                $prize->setLanguage(null);
            }
        }

        return $this;
    }
}
