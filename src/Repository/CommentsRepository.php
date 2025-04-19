<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Comments>
 *
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function add(Comments $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    public function update(Comments $comment): void
    {
        $entityManager = $this->getEntityManager();
        $existingComment = $this->find($comment->getCommentId());
        if (!$existingComment) {
            throw new \Exception('Comment not found');
        }
        $existingComment->setComment($comment->getComment());
        $existingComment->setUpdatedAt(new \DateTime());
        $entityManager->flush();
    }

    public function delete(int $id): void
    {
        $entityManager = $this->getEntityManager();
        $comment = $entityManager->getReference(Comments::class, $id);

        if ($comment) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }
    }

    public function listAll(): array
    {
        return $this->findAll();
    }

    public function fetchById(int $postId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c.commentId, c.comment , c.commentedAt, c.updatedAt, 
                    u.name, u.lastName, c.commenterId
             FROM App\Entity\Comments c
             JOIN App\Entity\Users u WITH c.commenterId = u.userId
             WHERE c.postId = :postId
             ORDER BY c.commentedAt DESC'
        )
            ->setParameter('postId', $postId);

        return $query->getResult();
    }
}
