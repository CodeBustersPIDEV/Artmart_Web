<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Productreview
 *
 * @ORM\Table(name="productreview", indexes={@ORM\Index(name="user_ID", columns={"user_ID"}), @ORM\Index(name="ready_product_ID", columns={"ready_product_ID"})})
 * @ORM\Entity
 */
class Productreview
{
    /**
     * @var int
     *
     * @ORM\Column(name="review_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reviewId;

    /**
     * @var int
     *
     * @ORM\Column(name="ready_product_ID", type="integer", nullable=false)
     */
    private $readyProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_ID", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    private $text;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", precision=10, scale=0, nullable=false)
     */
    private $rating;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $date = 'current_timestamp()';

    public function getReviewId(): ?int
    {
        return $this->reviewId;
    }

    public function getReadyProductId(): ?int
    {
        return $this->readyProductId;
    }

    public function setReadyProductId(int $readyProductId): self
    {
        $this->readyProductId = $readyProductId;

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
