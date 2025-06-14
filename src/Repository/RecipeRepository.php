<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    //Je crée une méthode personnalisée
    //créer une fonction avec en paramètre l'id de la catégorie et le nb max de recettes à retourner
    public function findTopPublishedByCategory($categoryId, $limit = 5): array
    {

        //QueryBuilder permet de construire une requête SQL de manière fluide et sécurisée
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :cat') //condition la catégorie associé à la recette = valeur du paramètre :cat (défini ci-dessous)
            ->andWhere('r.isPublished = true') //condition recupère que les recette publiées 
            ->setParameter('cat', $categoryId) // assigne la valeur au param :cat
            ->orderBy('r.createdAt', 'DESC') // trie par date de creation (les + récente)
            ->setMaxResults($limit) //on limite à 4 résultats
            ->getQuery() //on finalise
            ->getResult(); //on exécute
    }

    public function findMostLiked(int $limit = 5)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.likes', 'l')
            ->groupBy('r.id')
            ->orderBy('COUNT(l.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch($search){

        return $this->createQueryBuilder('r')
        ->where('r.title LIKE :search') // condition SQL qui filtre les recettes dont le title correspond au motif LIKE :search
        ->setParameter('search', '%'.$search.'%')
        ->getQuery()
        ->getResult();
    }

    public function countPublishedRecipes(): int
    {
        return (int) $this->count([
            'isPublished' => true
        ]);
    }

    public function countUnpublishedRecipes(): int
    {
        return (int) $this->count([
            'isPublished' => false
        ]);
    }

    public function findMostCommented(int $limit = 5)
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.comments', 'c')
            ->groupBy('r.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
