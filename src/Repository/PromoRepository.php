<?php

namespace App\Repository;

use App\Entity\Promo;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Promo>
 */
class PromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promo::class);
    }

    public function add(Promo $promo): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($promo);
        $entityManager->flush();
    }

    public function update(Promo $promo): void
    {
        $entityManager = $this->getEntityManager();
        $existingPromo = $this->find($promo->getPromoid());
        if (!$existingPromo) {
            throw new \Exception('Promo not found');
        }
        $existingPromo->setCodepromo($promo->getCodepromo());
        $existingPromo->setDateexpiration($promo->getDateexpiration());
        $existingPromo->setPourcentagepromo($promo->getPourcentagepromo());
        $existingPromo->setNombremaxpersonne($promo->getNombremaxpersonne());
        $entityManager->flush();
    }

    public function delete(Promo $promo): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($promo);
        $entityManager->flush();
    }

    public function listAll(): array
    {
        return $this->findAll();
    }
}
