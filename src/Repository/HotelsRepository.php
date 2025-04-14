<?php

namespace App\Repository;

use App\Entity\Hotels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class HotelsRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Hotels::class);
        $this->entityManager = $entityManager;
    }

    public function add(Hotels $hotel, bool $flush = true): void
    {
        $this->entityManager->persist($hotel);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function update(Hotels $hotel, bool $flush = true): void
    {
        $this->entityManager->persist($hotel);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function delete(Hotels $hotel, bool $flush = true): void
    {
        $this->entityManager->remove($hotel);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findAllHotels(): array
    {
        return $this->findAll();
    }
}
