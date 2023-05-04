<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Table(name: "customproduct", options: [
    'indexes' => [
        'client_ID' => ['columns' => ['client_ID']],
        'product_ID' => ['columns' => ['product_ID']]
    ]
])]
#[ORM\Entity]
class Customproduct 
{
    #[ORM\Column(name: "custom_product_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[Groups("custom_product")]
    private $customProductId;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "client_ID", referencedColumnName: "user_ID")]
    #[Groups("custom_product")]
    private $client;

    #[ORM\ManyToOne(targetEntity: "Product", cascade: ["remove"])]
    #[ORM\JoinColumn(name: "product_ID", referencedColumnName: "product_ID")]
    #[Groups("custom_product")]
    private $product;
    
    public function getCustomProductId(): ?int
    {
        return $this->customProductId;
    }

    public function getClient(): ?User
    {
        return $this->client ?? null;
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

    private $productId;
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

}