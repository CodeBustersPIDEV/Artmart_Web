<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Orderrefund
 *
 * @ORM\Table(name="orderrefund", indexes={@ORM\Index(name="OrderID", columns={"OrderID"})})
 * @ORM\Entity
 */
class Orderrefund
{
    /**
     * @var int
     *
     * @ORM\Column(name="orderRefund_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderrefundId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RefundAmount", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $refundamount = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="Reason", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $reason = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="Date", type="date", nullable=true, options={"default"="NULL"})
     */
    private $date = 'NULL';

    /**
     * @var \Order
     *
     * @ORM\ManyToOne(targetEntity="Order")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="OrderID", referencedColumnName="order_ID")
     * })
     */
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
