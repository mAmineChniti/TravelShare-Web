<?php

namespace App\Repository;

use App\Entity\Likes;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Likes>
 *
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function likesCounter(int $postId): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(l.postId) 
             FROM App\Entity\Likes l
             WHERE l.postId = :postId'
        )
        ->setParameter('postId', $postId);

        return (int) $query->getSingleScalarResult();
    }

    public function isLikedByUser(int $userId, int $postId): bool
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(l.postId) 
             FROM App\Entity\Likes l
             WHERE l.likerId = :userId AND l.postId = :postId'
        )
        ->setParameter('userId', $userId)
        ->setParameter('postId', $postId);

        return (int) $query->getSingleScalarResult() > 0;
    }

    public function addOrRemove(Likes $like): void
    {
        $entityManager = $this->getEntityManager();
        $isLiked = $this->isLikedByUser($like->getLikerId(), $like->getPostId());

        if ($isLiked) {
            $query = $entityManager->createQuery(
                'DELETE FROM App\Entity\Likes l
                 WHERE l.likerId = :likerId AND l.postId = :postId'
            )
            ->setParameter('likerId', $like->getLikerId())
            ->setParameter('postId', $like->getPostId());
            $query->execute();
        } else {
            $entityManager->persist($like);
            $entityManager->flush();
        }
    }
}
