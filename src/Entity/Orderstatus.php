<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Orderstatus
 *
 * @ORM\Table(name="orderstatus", indexes={@ORM\Index(name="OrderID", columns={"OrderID"})})
 * @ORM\Entity
 */
class Orderstatus
{
    /**
     * @var int
     *
     * @ORM\Column(name="orderStatus_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderstatusId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Status", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $status = 'NULL';

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


}
