<?php

namespace App\Repository;

use App\Entity\OffresVoyage;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
        $existingFlight = $this->find($offresVoyage->getOffresVoyageId());
        if (!$existingFlight) {
            throw new \Exception('Flight not found!');
        }
        $existingFlight
        ->setTitre($offresVoyage->getTitre())
        ->setDestination($offresVoyage->getDestination())
        ->setDescription($offresVoyage->getDescription())
        ->setDateDepart($offresVoyage->getDateDepart())
        ->setDateRetour($offresVoyage->getDateRetour())
        ->setPrix($offresVoyage->getPrix())
        ->setPlacesDisponibles($offresVoyage->getPlacesDisponibles())
        ;

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

    public function findByDestination(string $destination): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.destination LIKE :destination')
            ->setParameter('destination', '%'.$destination.'%')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(?\DateTime $departureDate, ?\DateTime $returnDate): array
    {
        $qb = $this->createQueryBuilder('o');

        if ($departureDate) {
            $qb->andWhere('o.dateDepart >= :departureDate')
               ->setParameter('departureDate', $departureDate);
        }

        if ($returnDate) {
            $qb->andWhere('o.dateRetour <= :returnDate')
               ->setParameter('returnDate', $returnDate);
        }

        return $qb->getQuery()->getResult();
    }
}
