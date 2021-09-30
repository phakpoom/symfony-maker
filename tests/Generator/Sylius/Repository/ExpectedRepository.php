<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class RepositoryGeneratorTestRepository extends EntityRepository implements RepositoryGeneratorTestRepositoryInterface
{
    public function createAdminListQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder;
    }
}
