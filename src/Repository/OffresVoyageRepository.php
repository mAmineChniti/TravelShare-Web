<?php

namespace App\Repository;

use App\Entity\OffresVoyage;
use App\Entity\Reponses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OffresVoyageRepository  extends ServiceEntityRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(OffresVoyage $offreVoyage): void
    {
        $this->em->persist($offreVoyage);
        $this->em->flush();
    }

    public function update(OffresVoyage $offreVoyage): void
    {
        $existing = $this->em->getRepository(OffresVoyage::class)->find($offreVoyage->getId());

        if (!$existing) {
            throw new EntityNotFoundException('Offre not found.');
        }

        $existing->setTitre($offreVoyage->getTitre());
        $existing->setDestination($offreVoyage->getDestination());
        $existing->setDescription($offreVoyage->getDescription());
        $existing->setDateDepart($offreVoyage->getDateDepart());
        $existing->setDateRetour($offreVoyage->getDateRetour());
        $existing->setPrix($offreVoyage->getPrix());
        $existing->setPlacesDisponibles($offreVoyage->getPlacesDisponibles());

        $this->em->flush();
    }

    public function delete(int $id): void
    {
        $offre = $this->em->getRepository(OffresVoyage::class)->find($id);
        if (!$offre) {
            throw new EntityNotFoundException('Offre not found.');
        }

        $this->em->remove($offre);
        $this->em->flush();
    }

    /**
     * @return OffresVoyage[]
     */
    public function listAll(): array
    {
        return $this->em->getRepository(OffresVoyage::class)->findAll();
    }
}
