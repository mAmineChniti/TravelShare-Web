<?php

namespace App\Repository;

use App\Entity\Hotels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HotelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotels::class);
    }

    public function add(Hotels $hotel): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($hotel);
        $entityManager->flush();
    }

    public function update(Hotels $hotel): void
    {
        $entityManager = $this->getEntityManager();
        $existingHotel = $this->find($hotel->getHotelId());
        if (!$existingHotel) {
            throw new \Exception('Hotel not found');
        }
        $existingHotel->setNom($hotel->getNom());
        $existingHotel->setAdress($hotel->getAdress());
        $existingHotel->setTelephone($hotel->getTelephone());
        $existingHotel->setCapaciteTotale($hotel->getCapaciteTotale());
        $existingHotel->setImageH($hotel->getImageH());
        $entityManager->flush();
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
}