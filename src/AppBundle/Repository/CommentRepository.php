<?php

namespace AppBundle\Repository;
use AppBundle\Repository\BaseRepository;

/**
 * CommentRepository
 */
class CommentRepository extends BaseRepository
{
    /**
     * Get All comments by Raport
     * @param array $fields
     * @param null $offset
     * @param null $limit
     * @param string $sortingColumn
     * @param string $sorting
     * @return array
     */
    public function getRaportComments($reportId, $offset = NULL, $limit = NULL)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c, owner')
            ->innerJoin('c.owner', 'owner')
            ->where('c.active = 1')
            ->andWhere('c.report = :reportId')
            ->setParameter('reportId', $reportId);

        return $this->wrapResult($query, 'c', $offset, $limit);
    }
}
