<?php

namespace App\Repository;

use App\Entity\Dish;
use DateTime;
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
        $dql = 'SELECT COUNT(d.id) FROM App\Entity\Dish d';
        $query = $this->getEntityManager()->createQuery($dql);

        return $query->getSingleScalarResult();
    }

    public function findAllFromRequest(array $fields)
    {
        $dql = 'SELECT d FROM App\Entity\Dish d ';
        if (!empty($fields['category']))
        {
            $dql .= 'LEFT JOIN d.category c ';
        }

        $hasWhere = false;
        if (!empty($fields['tags']))
        {
            $hasWhere = true;
            $dql .= 'WHERE d.id IN
                (SELECT d1.id FROM App\Entity\Dish d1 JOIN d1.tags t1
                WHERE t1.id IN (';
            $dql .= implode(',', $fields['tags']) . ') ';
            $dql .= 'GROUP BY d1.id HAVING COUNT(DISTINCT t1.id) = ' . count($fields['tags']) . ') ';
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
            $time = new DateTime();
            $time->setTimestamp($fields['diff_time']);
            $time = $time->format('Y-m-d H:m:s');

            if (!$hasWhere)
            {
                $hasWhere = true;
                $dql .= 'WHERE ';
            }

            else
            {
                $dql .= 'AND ';
            }

            $dql .= "(d.createdAt > '$time' OR d.updatedAt > '$time' OR d.deletedAt > '$time') ";
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
    }
}
