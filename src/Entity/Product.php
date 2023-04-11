<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="category_ID", columns={"category_ID"})})
 * @ORM\Entity
 */
#[ORM\Table(name: "product",options: [
    'indexes' => [
        'category_ID' => ['columns' => ['category_ID']]
    ]
])]
#[ORM\Entity]
class Product
{
    #[ORM\Column(name: "product_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $productId;

    #[ORM\Column(name: "category_ID", type: "integer",nullable: false)]
    private $categoryId;

    #[Assert\NotBlank]
    #[ORM\Column(name: "name", type: "string",length:255, nullable: false)]
    private $name;

    #[Assert\NotBlank]
    #[ORM\Column(name: "description", type: "text", length:65535,nullable: false)]
    private $description;

    #[Assert\NotBlank]
    #[ORM\Column(name: "dimensions", type: "string", length:255,nullable: false)]
    private $dimensions;

    #[Assert\NotBlank]
    #[ORM\Column(name: "weight", type: "decimal", precision:10, scale:2,nullable: false)]
    private $weight;

    #[Assert\NotBlank]
    #[ORM\Column(name: "material", type: "string", length:255, nullable: false)]
    private $material;

    #[ORM\Column(name: "image", type: "string", length:255, nullable: false)]
    private $image = 'imagecustom/imagec.png';

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(string $dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(string $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function __toString(): string
        {
            return $this->getName();
        }
        













    /**
        * @var Categories
        *
        * @ORM\ManyToOne(targetEntity="App\Entity\Categories")
        * @ORM\JoinColumn(name="category_ID", referencedColumnName="categories_ID", nullable=false)
        */
       private $category;
   
       public function getCategory(): ?Categories
       {
           return $this->category;
       }
   
       public function setCategory(Categories $category): self
       {
           $this->category = $category;
   
           return $this;
       }
   
}
