<?php

declare(strict_types=1);

namespace Test\Generator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DummyWhatIsThisCommand extends Command
{
    protected static $defaultName = 'test:dummy_what_is_this';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // addArgument, addOption
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // do stuff
        return 0;
    }
}
