<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "apply", options: [
    'indexes' => [
        'customproduct_ID' => ['columns' => ['customproduct_ID']],
        'artist_ID' => ['columns' => ['artist_ID']]
    ]
])]
class Apply
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "apply_ID", type: "integer")]
    private $applyId;

    #[ORM\Column(name: "status", type: "string", length: 255)]
    private $status;

    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "artist_ID", referencedColumnName: "user_ID")]
    private $artist;

    #[ORM\ManyToOne(targetEntity: "Customproduct")]
    #[ORM\JoinColumn(name: "customproduct_ID", referencedColumnName: "custom_product_ID")]
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