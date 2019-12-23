<?php

declare(strict_types=1);

namespace Test\Generator\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class DummyWhatIsThisListener
{
    public function __construct()
    {
    }

    public function methodName(ResourceControllerEvent $event)
    {
    }
}
