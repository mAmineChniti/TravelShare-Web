<?php

namespace App\Repository;

use App\Entity\PostImages;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class PostImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostImages::class);
    }

    public function add(PostImages $entity): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function findImagesByPostId(int $postId): array
    {
        $queryBuilder = $this->createQueryBuilder('pi')
            ->select('pi.image')
            ->where('pi.post = :postId')
            ->setParameter('postId', $postId);

        $results = $queryBuilder->getQuery()->getResult();

        return array_map(fn ($result) => stream_get_contents($result['image']), $results);
    }
}
