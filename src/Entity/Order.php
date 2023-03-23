<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="order", indexes={@ORM\Index(name="ShippingMethod", columns={"ShippingMethod"}), @ORM\Index(name="PaymentMethod", columns={"PaymentMethod"}), @ORM\Index(name="ProductID", columns={"ProductID"}), @ORM\Index(name="UserID", columns={"UserID"})})
 * @ORM\Entity
 */
class Order
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Quantity", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $quantity = NULL;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ShippingAddress", type="text", length=65535, nullable=true, options={"default"="NULL"})
     */
    private $shippingaddress = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="OrderDate", type="date", nullable=true, options={"default"="current_timestamp()"})
     */
    private $orderdate = 'current_timestamp()';

    /**
     * @var string|null
     *
     * @ORM\Column(name="TotalCost", type="decimal", precision=10, scale=2, nullable=true, options={"default"="NULL"})
     */
    private $totalcost = 'NULL';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="UserID", referencedColumnName="user_ID")
     * })
     */
    private $userid;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ProductID", referencedColumnName="product_ID")
     * })
     */
    private $productid;

    /**
     * @var \Shippingoption
     *
     * @ORM\ManyToOne(targetEntity="Shippingoption")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ShippingMethod", referencedColumnName="shippingOption_ID")
     * })
     */
    private $shippingmethod;

    /**
     * @var \Paymentoption
     *
     * @ORM\ManyToOne(targetEntity="Paymentoption")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="PaymentMethod", referencedColumnName="paymentOption_ID")
     * })
     */
    private $paymentmethod;

    public function getOrderId(): ?int
    {
        return $this->orderId;
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

    public function getShippingaddress(): ?string
    {
        return $this->shippingaddress;
    }

    public function setShippingaddress(?string $shippingaddress): self
    {
        $this->shippingaddress = $shippingaddress;

        return $this;
    }

    public function getOrderdate(): ?\DateTimeInterface
    {
        return $this->orderdate;
    }

    public function setOrderdate(?\DateTimeInterface $orderdate): self
    {
        $this->orderdate = $orderdate;

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

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getProductid(): ?Product
    {
        return $this->productid;
    }

    public function setProductid(?Product $productid): self
    {
        $this->productid = $productid;

        return $this;
    }

    public function getShippingmethod(): ?Shippingoption
    {
        return $this->shippingmethod;
    }

    public function setShippingmethod(?Shippingoption $shippingmethod): self
    {
        $this->shippingmethod = $shippingmethod;

        return $this;
    }

    public function getPaymentmethod(): ?Paymentoption
    {
        return $this->paymentmethod;
    }

    public function setPaymentmethod(?Paymentoption $paymentmethod): self
    {
        $this->paymentmethod = $paymentmethod;

        return $this;
    }


}
