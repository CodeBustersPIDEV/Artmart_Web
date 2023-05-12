<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Receipt
 *
 * @ORM\Table(name="receipt", indexes={@ORM\Index(name="OrderID", columns={"OrderID"}), @ORM\Index(name="ProductID", columns={"ProductID"})})
 * @ORM\Entity
 */
class Receipt
{
    /**
     * @var int
     *
     * @ORM\Column(name="receipt_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $receiptId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="OrderID", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $orderid = NULL;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ProductID", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $productid = NULL;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Quantity", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $quantity = NULL;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Price", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $price = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="Tax", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $tax = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="TotalCost", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $totalcost = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="Date", type="date", nullable=true, options={"default"="NULL"})
     */
    private $date = 'NULL';

    public function getReceiptId(): ?int
    {
        return $this->receiptId;
    }

    public function getOrderid(): ?int
    {
        return $this->orderid;
    }

    public function setOrderid(?int $orderid): self
    {
        $this->orderid = $orderid;

        return $this;
    }

    public function getProductid(): ?int
    {
        return $this->productid;
    }

    public function setProductid(?int $productid): self
    {
        $this->productid = $productid;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTax(): ?string
    {
        return $this->tax;
    }

    public function setTax(?string $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTotalcost(): ?string
    {
        return $this->totalcost;
    }

    public function setTotalcost(?string $totalcost): self
    {
        $this->totalcost = $totalcost;

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


}
