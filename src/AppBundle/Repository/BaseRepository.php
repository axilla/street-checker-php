<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * This Repository has defined some of basic functions we offten use.
 */
class BaseRepository extends EntityRepository
{
    /**
     * Get Object as Array withs specific fields
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function getObjectAsArray($id, $fields = array())
    {

        $fieldsString = $this->convertFieldsToQuery($fields, 'e');

        $query = $this->createQueryBuilder('e')
                      ->select($fieldsString)
                      ->where('e.id = :id')
                      ->andWhere('e.active = 1')
                      ->setParameter('id', $id)
                      ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Get list of object as Array with specific fields
     * @param array $fields
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getAllAsArray($fields = array(), $offset = NULL, $limit = NULL, $sorting = NULL)
    {

        $fieldsString = $this->convertFieldsToQuery($fields, 'e');

        $query = $this->createQueryBuilder('e')
                      ->select($fieldsString)
                      ->andWhere('e.active = 1');

        $query = $this->addFilters($query, 'e', $offset, $limit, $sorting);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Get All Active objects
     * @param string $sortAttr
     * @param string $direction
     * @return \Doctrine\ORM\Query
     */
    public function getAllActive($sortAttr = 'e.id', $direction = 'DESC')
    {
        $query = $this->createQueryBuilder('e')
                      ->select('e')
                      ->where('e.active = 1')
                      ->orderBy($sortAttr, $direction)
                      ->getQuery();

        return $query;
    }

    /**
     * Add usual filters on query
     * (Offset, Limit, Sorting, Sorting Column)
     * @param $query
     * @param $alias
     * @param $offset
     * @param $limit
     * @param null $sorting
     * @param null $sortingColumn
     * @return mixed
     */
    public function addFilters($query, $alias, $offset, $limit, $sortingColumn = 'id', $sorting = 'ASC')
    {

        if ($offset) {
            $query->setFirstResult($offset);
        }
        if ($limit) {
            $query->setMaxResults($limit);
        }

        if (!$sortingColumn) {
            $sortingColumn = 'id';
        }
        if (!$sorting) {
            $sorting = 'ASC';
        }

        $query->orderBy($alias . '.' . $sortingColumn, $sorting);
        return $query;
    }

    /**
     * Convert Array of fields to SQL query
     * @param $fields
     * @param $alias
     * @return string
     */
    public function convertFieldsToQuery($fields, $alias)
    {

        foreach ($fields as $key => $field) {
            if (!strpos($field, '.')) {
                $fields[$key] = $alias . '.' . $field;
            } else {
                $fields[$key] = $field;
            }
        }
        $fieldsString = implode(',', $fields);
        return $fieldsString;
    }

    public function wrapResult($query, $alias, $offset, $limit, $sortingColumn = 'id', $sorting = 'ASC')
    {
        $result = $this->addFilters($query, $alias, $offset, $limit, $sortingColumn, $sorting)
                       ->getQuery()
                       ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $count = $query->select("count($alias.id)")
                       ->getQuery()
                       ->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return array(
            'count' => $count,
            'list'  => $result
        );
    }

}
