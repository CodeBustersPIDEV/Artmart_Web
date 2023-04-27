<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "orderstatus",options: [
    'indexes' => [
        'OrderID' => ['columns' => ['OrderID']]
    ]
])]
#[ORM\Entity]
class Orderstatus
{
    #[ORM\Column(name: "orderStatus_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $orderstatusId;

    #[ORM\Column(name: "Status", type: "text",length:255, nullable: true,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter A Status For The Status")]
    private $status;

    #[ORM\Column(name: "Date", type: "date", nullable: true,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter A Date For The Status")]
    private $date;

    #[ORM\ManyToOne(targetEntity: "Order")]
    #[ORM\JoinColumn(name: "OrderID", referencedColumnName: "order_ID",options:["default"=>"NULL"])]
    private $orderid;

    public function getOrderstatusId(): ?int
    {
        return $this->orderstatusId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOrderid(): ?Order
    {
        return $this->orderid;
    }

    public function setOrderid(?Order $orderid): self
    {
        $this->orderid = $orderid;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getStatus();
    }
}
