<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "paymentoption")]
#[ORM\Entity]
class Paymentoption
{
    #[ORM\Column(name: "paymentOption_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $paymentoptionId;

    #[ORM\Column(name: "Name", type: "string",length:255, nullable: true,options:["default"=>"NULL"])]
    private $name;

    #[ORM\Column(name: "AvailableCountries", type: "string",length:255, nullable: true,options:["default"=>"NULL"])]
    private $availablecountries;

    public function getPaymentoptionId(): ?int
    {
        return $this->paymentoptionId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAvailablecountries(): ?string
    {
        return $this->availablecountries;
    }

    public function setAvailablecountries(?string $availablecountries): self
    {
        $this->availablecountries = $availablecountries;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
