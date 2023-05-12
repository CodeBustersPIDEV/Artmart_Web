<?php

namespace App\Entity;

use App\Repository\ArtistRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table('artist', options: ['indexes' => ['user_ID' => ['columns' => ['user_ID']]]])]
class Artist 
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'artist_ID', type: 'integer')]
    private int $artistId;

    #[ORM\Column(name: 'nbr_artwork', type: 'integer', nullable: false)]
    private int $nbrArtwork;


    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'NULL'])]
    private ?string $bio = 'NULL';

#[ORM\OneToOne(targetEntity: 'User',mappedBy:'artist')]
#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_ID')]

private ?User $user = null;

    public function getArtistId(): ?int
    {
        return $this->artistId;
    }
    public function getNbrArtwork(): ?int
    {
        return $this->nbrArtwork;
    }

   
    public function getUserId(): ?int
    {
        return $this->user->getUserId();
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
        return $this->user ?? null;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
