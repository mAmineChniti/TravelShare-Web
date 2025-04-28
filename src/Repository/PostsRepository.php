<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
        $existingPost = $this->find($post->getPostId());
        if (!$existingPost) {
            throw new \Exception('Post not found');
        }
        $existingPost->setTextContent($post->getTextContent());
        $existingPost->setUpdatedAt(new \DateTime());
        $entityManager->persist($existingPost);
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
            'SELECT p.postId, p.textContent, u.name, u.lastName, p.createdAt, p.ownerId, p.postTitle
         FROM App\Entity\Posts p 
         JOIN App\Entity\Users u WITH p.ownerId = u.userId 
         LEFT JOIN App\Entity\FlaggedContent f WITH p.postId = f.postId AND f.flaggerId = :userId 
         WHERE f.postId IS NULL 
         ORDER BY p.createdAt DESC'
        )
            ->setParameter('userId', $userId)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $posts = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $posts;
    }

    public function searchByTextContent(string $searchTerm, int $offset, int $limit): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p.postId, p.textContent, u.name, u.lastName, p.createdAt, p.ownerId, p.postTitle
             FROM App\\Entity\\Posts p 
             JOIN App\\Entity\\Users u WITH p.ownerId = u.userId 
             WHERE p.textContent LIKE :searchTerm 
             ORDER BY p.createdAt DESC'
        )
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->setFirstResult($offset)
        ->setMaxResults($limit);

        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function fetchPostsSorted(string $sortBy, int $offset, int $limit, int $userId): array
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('p.postId, p.textContent, u.name, u.lastName, p.createdAt, p.ownerId, p.postTitle')
            ->from('App\\Entity\\Posts', 'p')
            ->join('App\\Entity\\Users', 'u', 'WITH', 'p.ownerId = u.userId')
            ->leftJoin('App\\Entity\\FlaggedContent', 'f', 'WITH', 'p.postId = f.postId AND f.flaggerId = :userId')
            ->where('f.postId IS NULL')
            ->setParameter('userId', $userId)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ('date_asc' === $sortBy) {
            $queryBuilder->orderBy('p.createdAt', 'ASC');
        } elseif ('date_desc' === $sortBy) {
            $queryBuilder->orderBy('p.createdAt', 'DESC');
        } elseif ('hot' === $sortBy) {
            $queryBuilder->addSelect('(SELECT COUNT(l.postId) FROM App\\Entity\\Likes l WHERE l.postId = p.postId AND l.likeType = 1) AS HIDDEN likesCount')
                ->orderBy('likesCount', 'DESC');
        } else {
            $queryBuilder->orderBy('p.createdAt', 'DESC');
        }

        return $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}
