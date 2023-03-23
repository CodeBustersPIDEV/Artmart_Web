<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Wishlist
 *
 * @ORM\Table(name="wishlist", indexes={@ORM\Index(name="ProductID", columns={"ProductID"}), @ORM\Index(name="UserID", columns={"UserID"})})
 * @ORM\Entity
 */
class Wishlist
{
    /**
     * @var int
     *
     * @ORM\Column(name="wishlist_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $wishlistId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="UserID", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $userid = NULL;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ProductID", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $productid = NULL;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="Date", type="date", nullable=true, options={"default"="NULL"})
     */
    private $date = 'NULL';

    /**
     * @var int|null
     *
     * @ORM\Column(name="Quantity", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $quantity = NULL;

    /**
     * @var float|null
     *
     * @ORM\Column(name="Price", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $price = NULL;

    public function getWishlistId(): ?int
    {
        return $this->wishlistId;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(?int $userid): self
    {
        $this->userid = $userid;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }


}
