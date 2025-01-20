<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\NewsGrabber;

#[AsCommand(
    name: 'blog:news:import',
    description: 'import news from internet',
)]
class CrawlerCommand extends Command
{
    public function __construct(private readonly NewsGrabber $newsGrabber)
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('count',InputArgument::OPTIONAL,'Number of news')
            ->addOption('dryRun',null, InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $input->getArgument('count');
        $dryRun = (bool)$input->getOption('dryRun');
        $logger = new ConsoleLogger($output);
        $this->newsGrabber->setLogger($logger)->importNews($count,$dryRun);
        return Command::SUCCESS;
    }
}
