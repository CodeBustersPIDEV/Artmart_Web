<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artist
 *
 * @ORM\Table(name="artist", indexes={@ORM\Index(name="user_ID", columns={"user_ID"})})
 * @ORM\Entity
 */
class Artist
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="client_ID", type="integer")
     */
    private $artistId;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_artwork", type="integer", nullable=false)
     */
    private $nbrArtwork;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bio", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $bio = 'NULL';

    /**
     * @var \User
     *
     *@ORM\ManyToOne(targetEntity=User::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * 
     * 
     */
    private $user;

    public function getArtistId(): ?int
    {
        return $this->artistId;
    }
    public function getNbrArtwork(): ?int
    {
        return $this->nbrArtwork;
    }

    public function setNbrArtwork(int $nbrArtwork): self
    {
        $this->nbrArtwork = $nbrArtwork;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
