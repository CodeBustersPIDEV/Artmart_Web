<?php

namespace App\Entity;
use App\Repository\AdminRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table('admin', options: ['indexes' => ['user_ID' => ['columns' => ['user_ID']]]])]

class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'admin_ID', type: 'integer', nullable: false)]
    private int $adminId;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => 'NULL'])]
    private ?string $department = 'NULL';

    #[ORM\OneToOne(targetEntity: 'User',mappedBy:'artist')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_ID')]

    private ?User $user = null;


    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function getUserId(): ?int
    {
        return $this->user->getUserId();
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;

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
