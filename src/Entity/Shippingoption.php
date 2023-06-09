<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "Shippingoption")]
#[ORM\Entity]
class Shippingoption
{

    #[ORM\Column(name: "shippingOption_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $shippingoptionId;

    #[ORM\Column(name: "Name", type: "string", nullable: true,length:255)]
    #[Assert\NotBlank(message: "Please Enter A Name For The Shipping Option")]
    private $name;

    #[ORM\Column(name: "Carrier", type: "string", nullable: true,length:255)]
    #[Assert\NotBlank(message: "Please Enter A Carrier For The Shipping Option")]
    private $carrier;

    #[ORM\Column(name: "ShippingSpeed", type: "string", nullable: true,length:255)]
    #[Assert\NotBlank(message: "Please Enter A Shipping Speed For The Shipping Option")]
    private $shippingspeed;

    #[ORM\Column(name: "ShippingFee", type: "decimal", precision:10, scale:2,nullable: false)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private $shippingfee;

    #[ORM\Column(name: "AvailableRegions", type: "string", length:255, nullable: true,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter Available Regions For The Shipping Option")]
     private $availableregions;

    public function getShippingoptionId(): ?int
    {
        return $this->shippingoptionId;
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

    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    public function setCarrier(?string $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getShippingspeed(): ?string
    {
        return $this->shippingspeed;
    }

    public function setShippingspeed(?string $shippingspeed): self
    {
        $this->shippingspeed = $shippingspeed;

        return $this;
    }

    public function getShippingfee(): ?string
    {
        return $this->shippingfee;
    }

    public function setShippingfee(?string $shippingfee): self
    {
        $this->shippingfee = $shippingfee;

        return $this;
    }

    public function getAvailableregions(): ?string
    {
        return $this->availableregions;
    }

    public function setAvailableregions(?string $availableregions): self
    {
        $this->availableregions = $availableregions;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

}
