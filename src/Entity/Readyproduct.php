<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Readyproduct
 *
 * @ORM\Table(name="readyproduct", indexes={
 *     @ORM\Index(name="user_ID", columns={"user_ID"}),
 *     @ORM\Index(name="product_ID", columns={"product_ID"})
 * })
 * @ORM\Entity
 */
#[ORM\Table(name: "Readyproduct",options: [
    'indexes' => [
        'user_ID' => ['columns' => ['user_ID']],
        'product_ID' => ['columns' => ['product_ID']]
    ]
])]
#[ORM\Entity]
class Readyproduct
{
    #[ORM\Column(name: "ready_product_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $readyProductId;

    #[ORM\Column(name: "price", type: "integer", nullable: false)]
    private $price;

    #[ORM\ManyToOne(targetEntity: "Product")]
    #[ORM\JoinColumn(name: "product_ID", referencedColumnName: "product_ID")]
    private $productId;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "user_ID", referencedColumnName: "user_ID")]
    private $userId;

    public function getReadyProductId(): ?int
    {
        return $this->readyProductId;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
