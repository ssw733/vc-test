<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:count-post-views',
    description: 'Подсчитать количество просмотров по постам',
)]
class CountPostViewsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sql = 'UPDATE post as p INNER JOIN (SELECT COUNT(*) as count, post_id FROM user_posts GROUP BY post_id) as up SET p.views = up.count';
        //print_r($sql);die;
        $this->em->getConnection()->executeStatement($sql);

        $io->success('ok');

        return Command::SUCCESS;
    }
}
