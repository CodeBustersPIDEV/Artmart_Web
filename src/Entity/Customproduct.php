<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customproduct
 *
 * @ORM\Table(name="customproduct", indexes={@ORM\Index(name="client_ID", columns={"client_ID"}), @ORM\Index(name="product_ID", columns={"product_ID"})})
 * @ORM\Entity
 */
class Customproduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="custom_product_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customProductId;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_ID", referencedColumnName="user_ID")
     * })
     */
    private $client;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_ID", referencedColumnName="product_ID")
     * })
     */
    private $product;

    public function getCustomProductId(): ?int
    {
        return $this->customProductId;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }


}
