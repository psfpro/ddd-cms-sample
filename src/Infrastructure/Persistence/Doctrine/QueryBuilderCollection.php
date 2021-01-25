<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountOutputWalker;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Exception;

final class QueryBuilderCollection extends AbstractLazyCollection implements Selectable
{
    private QueryBuilder $queryBuilder;

    private ?int $count = null;

    /**
     * QueryBuilderCollection constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Do the initialization logic
     *
     * @return void
     */
    protected function doInitialize()
    {
        $this->collection = new ArrayCollection($this->queryBuilder->getQuery()->getResult());
    }

    public function slice($offset, $length = null)
    {
        if (!$this->initialized) {
            return $this->queryBuilder->setFirstResult($offset)->setMaxResults($length)->getQuery()->getResult();
        }

        return parent::slice($offset, $length);
    }

    /**
     * Selects all elements from a selectable that match the expression and
     * returns a new collection containing these elements.
     *
     * @param Criteria $criteria
     *
     * @return Collection
     * @throws Query\QueryException
     */
    public function matching(Criteria $criteria)
    {
        $qb = clone $this->queryBuilder;
        $qb->addCriteria($criteria);

        return new static($qb);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if ($this->count === null) {
            try {
                $this->count = array_sum(array_map('current', $this->getCountQuery()->getScalarResult()));
            } catch (Exception $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }

    /**
     * Clones a query.
     *
     * @param Query $query The query.
     *
     * @return Query The cloned query.
     */
    private function cloneQuery(Query $query)
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());
        $cloneQuery->setCacheable(false);

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }

    /**
     * Determines whether to use an output walker for the query.
     *
     * @param Query $query The query.
     *
     * @return bool
     */
    private function useOutputWalker(Query $query)
    {
        return (bool)$query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER) === false;
    }

    /**
     * Appends a custom tree walker to the tree walkers hint.
     *
     * @param Query $query
     * @param string $walkerClass
     */
    private function appendTreeWalker(Query $query, $walkerClass)
    {
        $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);

        if ($hints === false) {
            $hints = [];
        }

        $hints[] = $walkerClass;
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
    }

    /**
     * Returns Query prepared to count.
     *
     * @return Query
     * @throws DBALException
     */
    private function getCountQuery()
    {
        $query = $this->queryBuilder->getQuery();
        /* @var $countQuery Query */
        $countQuery = $this->cloneQuery($query);

        if (!$countQuery->hasHint(CountWalker::HINT_DISTINCT)) {
            $countQuery->setHint(CountWalker::HINT_DISTINCT, true);
        }

        if ($this->useOutputWalker($countQuery)) {
            $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

            $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, CountOutputWalker::class);
            $countQuery->setResultSetMapping($rsm);
        } else {
            $this->appendTreeWalker($countQuery, CountWalker::class);
        }

        $countQuery->setFirstResult(null)->setMaxResults(null);

        $parser = new Parser($countQuery);
        $parameterMappings = $parser->parse()->getParameterMappings();
        /* @var $parameters Collection|Parameter[] */
        $parameters = $countQuery->getParameters();

        foreach ($parameters as $key => $parameter) {
            $parameterName = $parameter->getName();

            if (!(isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings))) {
                unset($parameters[$key]);
            }
        }

        $countQuery->setParameters($parameters);

        return $countQuery;
    }
}
