<?php

namespace App\Repository;

use App\Entity\Dish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    public function getNumberOfDishes(): int
    {
        $dql = 'SELECT COUNT(d.id) FROM App\\Entity\\Dish d';
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    public function findAllFromRequest(array $fields)
    {
        $dql = 'SELECT d FROM App\\Entity\\Dish d ';

        $hasWhere = false;
        if (!empty($fields['tags']))
        {
            $hasWhere = true;
            $dql .= 'JOIN d.tags t JOIN d.category c WHERE (';
            foreach ($fields['tags'] as $tag)
            {
                $dql .= "t.id=$tag AND ";
            }

            $dql .= '1=1) ';
        }

        if (!empty($fields['category']))
        {
            $category = $fields['category'];

            if (!$hasWhere)
            {
                $hasWhere = true;
                $dql .= 'WHERE ';
            }

            else
            {
                $dql .= 'AND ';
            }

            $dql .= 'c.id ';
            if ($category == 'NULL')
            {
                $dql .= 'IS NULL ';
            }

            else if ($category == '!NULL')
            {
                $dql .= 'IS NOT NULL ';
            }

            else
            {
                $dql .= "= $category ";
            }
        }

        if ($fields['diff_time'] > 0)
        {
            $diff_time = $fields['diff_time'];

            if (!$hasWhere)
            {
                $hasWhere = true;
                $dql .= 'WHERE ';
            }

            else
            {
                $dql .= 'AND ';
            }

            $dql .= "(TIMESTAMP(d.created_at) > $diff_time OR TIMESTAMP(d.updated_at) > $diff_time OR TIMESTAMP(d.deleted_at) > $diff_time) ";
        }

        $query = $this->getEntityManager()->createQuery($dql);
        if ($fields['per_page'] > 0)
        {
            $query->setMaxResults($fields['per_page']);

            if ($fields['page'] > 0)
            {
                $query->setFirstResult(($fields['page']-1) * $fields['per_page']);
            }
        }

        $paginator = new Paginator($query);
        return $paginator;
        // $query = $this->createQueryBuilder('d');

        // if ($fields['category'] == 'NULL')
        // {
        //     $query = $query->andWhere("d.category_id is NULL");
        // }

        // else if ($fields['category'] == '!NULL')
        // {
        //     $query = $query->andWhere("d.category_id is not NULL");
        // }

        // else if ($fields['category'] !== null)
        // {
        //     $category = $fields['category'];
        //     $query = $query->andWhere("d.category_id = $category");
        // }

        // if (!empty($fields['tags']))
        // {
        //     $query = $query->andWhere("d.id ")
        //     //$qb = $this->createQueryBuilder('d');
        //     foreach ($fields['tags'] as $tag)
        //     {
        //         //$qb->expr()->some()
        //         a
        //     }

            
        // }
    }

    // /**
    //  * @return Dish[] Returns an array of Dish objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dish
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
