<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Readyproduct
 *
 * @ORM\Table(name="readyproduct", indexes={@ORM\Index(name="user_ID", columns={"user_ID"}), @ORM\Index(name="product_ID", columns={"product_ID"})})
 * @ORM\Entity
 */
class Readyproduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="ready_product_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $readyProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="product_ID", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_ID", type="integer", nullable=false)
     */
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

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }


}
