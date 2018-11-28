<?php

declare(strict_types=1);

namespace App\App\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface aInterface extends ResourceInterface, TimestampableInterface, CodeAwareInterface, ToggleableInterface
{
}
