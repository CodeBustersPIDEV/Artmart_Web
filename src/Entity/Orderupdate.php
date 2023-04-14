<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "orderupdate",options: [
    'indexes' => [
        'OrderID' => ['columns' => ['OrderID']]
    ]
])]
#[ORM\Entity]
class Orderupdate
{
    #[ORM\Column(name: "orderUpdate_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $orderupdateId;

    #[ORM\Column(name: "UpdateMessage", type: "text",length:65535, nullable: true,options:["default"=>"NULL"])]
    private $updatemessage;

    #[ORM\Column(name: "Date", type: "date", nullable: true,options:["default"=>"NULL"])]
    private $date;

    #[ORM\ManyToOne(targetEntity: "Order")]
    #[ORM\JoinColumn(name: "OrderID", referencedColumnName: "order_ID")]
    private $orderid;

    public function getOrderupdateId(): ?int
    {
        return $this->orderupdateId;
    }

    public function getUpdatemessage(): ?string
    {
        return $this->updatemessage;
    }

    public function setUpdatemessage(?string $updatemessage): self
    {
        $this->updatemessage = $updatemessage;

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


}
