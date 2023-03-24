<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Apply
 *
 * @ORM\Table(name="apply", indexes={@ORM\Index(name="customproduct_ID", columns={"customproduct_ID"}), @ORM\Index(name="artist_ID", columns={"artist_ID"})})
 * @ORM\Entity
 */
class Apply
{
    /**
     * @var int
     *
     * @ORM\Column(name="apply_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $applyId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=false)
     */
    private $status;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="artist_ID", referencedColumnName="user_ID")
     * })
     */
    private $artist;

    /**
     * @var \Customproduct
     *
     * @ORM\ManyToOne(targetEntity="Customproduct")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customproduct_ID", referencedColumnName="custom_product_ID")
     * })
     */
    private $customproduct;

    public function getApplyId(): ?int
    {
        return $this->applyId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getArtist(): ?User
    {
        return $this->artist ?? null;
    }

    public function setArtist(?User $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getCustomproduct(): ?Customproduct
    {
        return $this->customproduct ?? null;
    }

    public function setCustomproduct(?Customproduct $customproduct): self
    {
        $this->customproduct = $customproduct;

        return $this;
    }


}