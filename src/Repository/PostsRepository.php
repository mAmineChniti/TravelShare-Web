<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    public function add(Posts $post): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();
    }

    public function addAndId(Posts $post): int
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $post->getPostId();
    }

    public function update(Posts $post): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->merge($post);
        $entityManager->flush();
    }

    public function delete(int $id): void
    {
        $entityManager = $this->getEntityManager();
        $post = $entityManager->getReference(Posts::class, $id);
        $entityManager->remove($post);
        $entityManager->flush();
    }

    public function listAll(): array
    {
        return $this->findAll();
    }

    public function fetchPosts(int $offset, int $limit, int $userId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p.postId, p.textContent, u.name, u.lastName 
             FROM App\Entity\Posts p 
             JOIN App\Entity\Users u WITH p.ownerId = u.userId 
             LEFT JOIN App\Entity\FlaggedContent f WITH p.postId = f.postId AND f.flaggerId = :userId 
             WHERE f.postId IS NULL 
             ORDER BY p.createdAt DESC'
        )
        ->setParameter('userId', $userId)
        ->setFirstResult($offset)
        ->setMaxResults($limit);

        return $query->getResult();
    }
}
