<?php

declare(strict_types=1);

namespace Test\Generator\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class DummyWhatIsThisListener
{
    public function __construct()
    {
    }

    public function onRequest(RequestEvent $event)
    {
    }
}
