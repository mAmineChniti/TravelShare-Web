<?php

namespace App\Repository;

use App\Entity\Hotels;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class HotelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotels::class);
    }

<<<<<<< HEAD
    //    /**
    //     * @return Hotels[] Returns an array of Hotels objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Hotels
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
=======
    public function add(Hotels $hotel): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($hotel);
        $entityManager->flush();
    }

    public function update(Hotels $hotel): void
    {
        $em = $this->getEntityManager();
        $existing = $this->find($hotel->getHotelId());
        if (!$existing) {
            throw new \Exception('Hotel not found');
        }

        $existing->setNom($hotel->getNom());
        $existing->setAdress($hotel->getAdress());
        $existing->setTelephone($hotel->getTelephone());
        $existing->setCapaciteTotale($hotel->getCapaciteTotale());
        $existing->setImageH($hotel->getImageH());
        $existing->setDescription($hotel->getDescription());

        $em->flush();
    }

    public function delete(int $id): void
    {
        $entityManager = $this->getEntityManager();
        $hotel = $entityManager->getReference(Hotels::class, $id);
        $entityManager->remove($hotel);
        $entityManager->flush();
    }

    public function listAll(): array
    {
        return $this->findAll();
    }
>>>>>>> origin/master
}
