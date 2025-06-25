<?php

namespace App\Command;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Post;

#[AsCommand(
    name: 'app:create-mock',
    description: 'Add a short description for your command',
)]
class CreateMockCommand extends Command
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
        ini_set('memory_limit', '128M');
        $io = new SymfonyStyle($input, $output);

        $this->getApplication()->doRun(new ArrayInput(['command' => 'doctrine:database:drop', '--force' => true]), $output);
        $this->getApplication()->doRun(new ArrayInput(['command' => 'doctrine:database:create']), $output);
        $this->getApplication()->doRun(new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]), $output);

        //Генерация постов
        $filesystem = new Filesystem();
        $words = $filesystem->readFile('var/russian.txt');
        $words = explode(PHP_EOL, $words);
        $glossaryLength = count($words);
        foreach (range(1, 100) as $i) {
            $post = new Post();
            $postData = '';
            foreach (range(1, 100) as $j) {
                $postData .= $words[rand(0, $glossaryLength - 1)] . ' ';
            }
            $post->setData($postData);
            $post->setHotness(rand(1, 100));
            $post->setTitle($words[rand(0, $glossaryLength - 1)]);
            $post->setTimestamp(rand(0, time()));
            $this->em->persist($post);
            $this->em->flush();
            print_r('generated post: ' . $post->getId() . PHP_EOL);
        }
        $io->success('Данные успешно сгенерированы!');

        return Command::SUCCESS;
    }
}
