<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table('client', options: ['indexes' => ['user_ID' => ['columns' => ['user_ID']]]])]

class Client 
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'client_ID', type: 'integer')]
    private int $clientId;

    #[ORM\Column(name: 'nbr_orders', type: 'integer', nullable: false)]
    private int $nbrOrders;

    #[ORM\Column(name: 'nbr_demands', type: 'integer', nullable: false)]
    private int $nbrDemands;

    #[ORM\OneToOne(targetEntity: 'User',mappedBy:'client')]
    #[ORM\JoinColumn(name:'user_ID', referencedColumnName: 'user_ID')]

    private ?User $user = null;

    public function getClientId(): ?int
    {
        return $this->clientId;
    }
    public function setClientId(int $ClientId): self
    {
        $this->clientId = $ClientId;

        return $this;
    }
    public function getUserId(): ?int
    {
        return $this->user->getUserId();
    }
   

    public function getNbrOrders(): ?int
    {
        return $this->nbrOrders;
    }

    public function setNbrOrders(int $nbrOrders): self
    {
        $this->nbrOrders = $nbrOrders;

        return $this;
    }

    public function getNbrDemands(): ?int
    {
        return $this->nbrDemands;
    }

    public function setNbrDemands(int $nbrDemands): self
    {
        $this->nbrDemands = $nbrDemands;

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
