<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "`order`",options: [
    'indexes' => [
        'ShippingMethod' => ['columns' => ['ShippingMethod']],
        'PaymentMethod' => ['columns' => ['PaymentMethod']],
        'ProductID' => ['columns' => ['ProductID']],
        'UserID' => ['columns' => ['UserID']]
    ]
])]
#[ORM\Entity]
class Order
{
    #[ORM\Column(name: "order_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $orderId;

    #[ORM\Column(name: "Quantity", type: "integer", nullable: false,options:["default"=>"NULL"])]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "Please Enter A Quantity For The Order")]
    private $quantity;

    #[ORM\Column(name: "ShippingAddress", type: "text",length:65535, nullable: false,options:["default"=>"NULL"])]
    #[Assert\NotBlank(message: "Please Enter A Shipping Address For The Order")]
    private $shippingaddress;


    #[ORM\Column(name: "OrderDate", type: "date", nullable: true,options:["default"=>"CURRENT_TIMESTAMP"])]
    #[Assert\NotBlank(message: "Please Enter A Date For The Order")]
    private $orderdate;

    #[ORM\Column(name: "TotalCost", type: "decimal", nullable: true, precision:10, scale:2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "Please Enter A Cost For The Order")]
    private $totalcost;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "user_ID")]
    private $userid;

    #[ORM\ManyToOne(targetEntity: "Product")]
    #[ORM\JoinColumn(name: "ProductID", referencedColumnName: "product_ID")]
    private $productid;

    #[ORM\ManyToOne(targetEntity: "Shippingoption")]
    #[ORM\JoinColumn(name: "ShippingMethod", referencedColumnName: "shippingOption_ID")]
    private $shippingmethod;

    #[ORM\ManyToOne(targetEntity: "Paymentoption")]
    #[ORM\JoinColumn(name: "PaymentMethod", referencedColumnName: "paymentOption_ID")]
    private $paymentmethod;

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(?int $id): self
    {
        $this->orderId = $id;

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

    public function getShippingaddress(): ?string
    {
        return $this->shippingaddress;
    }

    public function setShippingaddress(?string $shippingaddress): self
    {
        $this->shippingaddress = $shippingaddress;

        return $this;
    }

    public function getOrderdate(): ?string
    {
        return $this->orderdate instanceof \DateTimeInterface ? $this->orderdate->format('Y-m-d H:i:s') : null;
    }

    public function setOrderdate($orderdate): self
    {
        if (is_string($orderdate)) {
            $orderdate = \DateTime::createFromFormat('Y-m-d', $orderdate);
        }
    
        if (!($orderdate instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException(sprintf('Expected a \DateTimeInterface object, got "%s"', gettype($orderdate)));
        }
    
        $this->orderdate = $orderdate;
    
        return $this;
    } 
    public function setupOrderDate(string $orderDate): void
    {
        $this->orderdate = new DateTime($orderDate);
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

    public function __toString(): string
    {
        return $this->getShippingaddress();
    }
}
