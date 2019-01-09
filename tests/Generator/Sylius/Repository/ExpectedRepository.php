<?php

declare(strict_types=1);

namespace Test\Generator\Sylius\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class RepositoryGeneratorTestRepository extends EntityRepository implements RepositoryGeneratorTestRepositoryInterface
{
}
