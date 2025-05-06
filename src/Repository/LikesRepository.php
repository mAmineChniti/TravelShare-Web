<?php

namespace App\Repository;

use App\Entity\Likes;
use App\Entity\Posts;
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
             FROM App\\Entity\\Likes l
             WHERE l.postId = :postId AND l.likeType = 1'
        )
        ->setParameter('postId', $postId);

        return (int) $query->getSingleScalarResult();
    }

    public function dislikesCounter(int $postId): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(l.postId) 
             FROM App\\Entity\\Likes l
             WHERE l.postId = :postId AND l.likeType = 0'
        )
        ->setParameter('postId', $postId);

        return (int) $query->getSingleScalarResult();
    }

    public function isLikedByUser(int $userId, int $postId): ?bool
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT l.likeType 
             FROM App\\Entity\\Likes l
             WHERE l.likerId = :userId AND l.postId = :postId'
        )
        ->setParameter('userId', $userId)
        ->setParameter('postId', $postId);
        $result = $query->getOneOrNullResult();

        return null !== $result ? (bool) $result['likeType'] : null;
    }

    public function handleVote(int $userId, int $postId, bool $likeType): void
    {
        $entityManager = $this->getEntityManager();
        $existingLike = $this->findOneBy(['likerId' => $userId, 'postId' => $postId]);

        if ($existingLike) {
            if ($existingLike->getLikeType() === $likeType) {
                $entityManager->remove($existingLike);
            } else {
                $existingLike->setLikeType($likeType);
                $entityManager->persist($existingLike);
            }
        } else {
            $newLike = new Likes();
            $newLike->setLikerId($userId);
            $newLike->setPostId($postId);
            $newLike->setLikeType($likeType);

            $post = $entityManager->getRepository(Posts::class)->find($postId);
            if (!$post) {
                throw new \Exception('Post not found for ID: '.$postId);
            }
            $newLike->setPost($post);

            $entityManager->persist($newLike);
        }

        $entityManager->flush();
    }
}
