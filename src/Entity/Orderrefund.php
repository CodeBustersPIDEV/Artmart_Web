<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "orderrefund",options: [
    'indexes' => [
        'OrderID' => ['columns' => ['OrderID']]
    ]
])]
#[ORM\Entity]
class Orderrefund
{
    #[ORM\Column(name: "orderRefund_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $orderrefundId;

    #[ORM\Column(name: "RefundAmount", type: "decimal",precision:10, scale:2, nullable: true,options:["default"=>"NULL"])]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "Please Enter A Refund Amount For The Refund")]
    private $refundamount;

    #[ORM\Column(name: "Reason", type: "text",length:65535, nullable: true,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter A Reason For The Refund")]
    private $reason;

    #[ORM\Column(name: "Date", type: "date", nullable: true,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter A Date For The Refund")] 
    private $date;

    #[ORM\ManyToOne(targetEntity: "Order")]
    #[ORM\JoinColumn(name: "OrderID", referencedColumnName: "order_ID")]
    private $orderid;

    public function getOrderrefundId(): ?int
    {
        return $this->orderrefundId;
    }

    public function getRefundamount(): ?string
    {
        return $this->refundamount;
    }

    public function setRefundamount(?string $refundamount): self
    {
        $this->refundamount = $refundamount;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

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
