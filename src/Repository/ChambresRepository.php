<?php

namespace App\Repository;

use App\Entity\Chambres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chambres>
 *
 * @method Chambres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chambres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chambres[]    findAll()
 * @method Chambres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChambresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambres::class);
    }

    public function add(Chambres $chambre): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($chambre);
        $entityManager->flush();
    }

    public function update(Chambres $chambre): void
    {
        $entityManager = $this->getEntityManager();
        $existingChambre = $this->find($chambre->getChambreId());
        if (!$existingChambre) {
            throw new \Exception('Chambre not found');
        }
        $existingChambre->setNumeroChambre($chambre->getNumeroChambre());
        $existingChambre->setTypeEnu($chambre->getTypeEnu());
        $existingChambre->setPrixParNuit($chambre->getPrixParNuit());
        $existingChambre->setDisponible($chambre->getDisponible());
        $existingChambre->setHotel($chambre->getHotel());
        $entityManager->flush();
    }

    public function delete(int $id): void
    {
        $entityManager = $this->getEntityManager();
        $chambre = $this->find($id);
        if (!$chambre) {
            throw new \Exception('Chambre not found');
        }
        $chambre->setHotel(null);
        $entityManager->remove($chambre);
        $entityManager->flush();
    }

    public function listAll(): array
    {
        return $this->findAll();
    }

    public function listByHotelId(int $hotelId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.hotel', 'h')
            ->addSelect('h')
            ->where('c.hotel = :hotelId')
            ->setParameter('hotelId', $hotelId)
            ->getQuery()
            ->getResult();
    }
}
