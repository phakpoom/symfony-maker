<?php

declare(strict_types=1);

namespace Test\Generator\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

final class DummyWhatIsThisListener
{
    public function __construct()
    {
    }

    public function postPersist(LifecycleEventArgs $args)
    {
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
    }

    public function preRemove(LifecycleEventArgs $args)
    {
    }
}
