<?php

namespace App\Entity;

use App\Repository\PrizeRepository;
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
}
