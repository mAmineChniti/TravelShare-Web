<?php

namespace App\Repository;

use App\Entity\OffresVoyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffresVoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffresVoyage::class);
    }

    public function add(OffresVoyage $offresVoyage): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($offresVoyage);
        $entityManager->flush();
    }

    public function update(OffresVoyage $offresVoyage): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    public function delete(OffresVoyage $offresVoyage): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($offresVoyage);
        $entityManager->flush();
    }

    public function findAllOffres(): array
    {
        return $this->findAll();
    }
}