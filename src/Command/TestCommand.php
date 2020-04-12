<?php
declare(strict_types=1);

namespace App\Command;

use App\ApiService\PickPointApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    /** @var PickPointApi */
    private $pickPointApi;

    public function __construct(PickPointApi $pickPointApi)
    {
        $this->pickPointApi = $pickPointApi;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$io->note('It\'s test command!');
        //$io->error('Error text.');

        $result = $this->pickPointApi->getReestrNumber();
        dump($result); die('ok');

        return 0;
    }
}
