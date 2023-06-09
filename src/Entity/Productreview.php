<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "productreview", options: [
    'indexes' => [
        'user_ID' => ['columns' => ['user_ID']],
        'ready_product_ID' => ['columns' => ['ready_product_ID']]
    ]
])]
#[ORM\Entity]
class Productreview
{
    #[ORM\Column(name: "review_ID", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $reviewId;

    #[ORM\ManyToOne(targetEntity: "Readyproduct")]
    #[ORM\JoinColumn(name: "ready_product_ID", referencedColumnName: "ready_product_ID", nullable: false)]
    private $readyProductId;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "user_ID", referencedColumnName: "user_ID", nullable: false)]
    private $userId;

    #[ORM\Column(name: "title", type: "string", nullable: false, length: 255)]
    private $title;

    #[ORM\Column(name: "text", type: "text", nullable: false, length: 65535)]
    private $text;

    #[ORM\Column(name: "rating", type: "float", precision: 10, scale: 0, nullable: false, length: 65535)]
    private $rating;

    #[ORM\Column(name: "date", type: "datetime", options: ["default" => "current_timestamp()"], nullable: false, length: 65535)]
    private $date = 'current_timestamp()';

    public function getReviewId(): ?int
    {
        return $this->reviewId;
    }

    public function getReadyProductId(): ?Readyproduct
    {
        return $this->readyProductId;
    }

    public function setReadyProductId(Readyproduct $readyProductId): self
    {
        $this->readyProductId = $readyProductId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date instanceof \DateTimeInterface ? $this->date->format('Y-m-d H:i:s') : null;;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function __toString(): string
    {
        return $this->readyProductId;
    }
}
