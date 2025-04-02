<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Rubrik;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param Rubrik $rubrik
     * @return Post[]
     */
    
      // Méthodee pour récuperer touts les posts associées à une rubrique 
    public function findByRubrik(Rubrik $rubrik): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.rubrik = :rubrik')
            ->setParameter('rubrik', $rubrik)
            ->getQuery()
            ->getResult();
    }  
    /**
     * Méthode pour récupérer les posts paginés et filtrés
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return Post[]
     */
    public function findFilteredPaginatedPosts(array $filters, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC') // Trier du plus récent au plus ancien
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        // Appliquer le filtre par rubrique si fourni
        if (!empty($filters['rubrik'])) {
            $qb->andWhere('p.rubrik = :rubrik')
               ->setParameter('rubrik', $filters['rubrik']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode pour compter le nombre total d'articles filtrés
     * @param array $filters
     * @return int
     */
    public function countFilteredPosts(array $filters): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        // Appliquer le filtre par rubrique si fourni
        if (!empty($filters['rubrik'])) {
            $qb->andWhere('p.rubrik = :rubrik')
               ->setParameter('rubrik', $filters['rubrik']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
   