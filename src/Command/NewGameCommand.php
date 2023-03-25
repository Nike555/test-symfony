<?php

namespace App\Command;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command which will create new game
 *     $ symfony console app:new-game --date="2023-03-25" --name="First game"
 * Where attribute:
 *  - date: date when start game (default: current day)
 *  - name: name of game (default: New Game)
 */
#[AsCommand(
    name: 'app:new-game',
    description: 'Create new game'
)]
class NewGameCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the game', 'New Game')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Date when game will start',  (new \DateTime())->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $startDate = \DateTime::createFromFormat('Y-m-d', $input->getOption('date'));
        $endDate = $this->getEndDate($startDate);

        $existGameInInterval = $this->checkExistOnDateInterval($startDate, $endDate);
        if ($existGameInInterval) {
            $output->writeln('Game was NOT created! Because on this days already exist game.');
        }
        else {
            $game = new Game();
            $game->setName($name);
            $game->setStartDate($startDate);
            $game->setEndDate($endDate);

            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $output->writeln('Game was created!');
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }

    private function getEndDate(\DateTimeInterface $startDate) :\DateTimeInterface
    {
        $endDate = \DateTime::createFromInterface($startDate);
        $endDate->modify('+1 day');
        return $endDate;
    }

    private function checkExistOnDateInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate) :bool
    {
        $gameRepository = $this->entityManager->getRepository(Game::class);
        $existGame = $gameRepository->createQueryBuilder('g')
            ->where('g.start_date BETWEEN :start AND :end')
            ->orWhere('g.end_date BETWEEN :start AND :end')
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->getQuery()
            ->execute();
        return (bool)$existGame;
    }

}