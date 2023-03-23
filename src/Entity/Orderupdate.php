<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Orderupdate
 *
 * @ORM\Table(name="orderupdate", indexes={@ORM\Index(name="OrderID", columns={"OrderID"})})
 * @ORM\Entity
 */
class Orderupdate
{
    /**
     * @var int
     *
     * @ORM\Column(name="orderUpdate_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderupdateId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="UpdateMessage", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $updatemessage = 'NULL';

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
