<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Prize;
use Doctrine\ORM\EntityManagerInterface;

class CreateGameService
{
    private string $responseMessage;
    private string $name;
    private string $date;
    private \DateTimeInterface $startDate;
    private \DateTimeInterface $endDate;

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function create(): bool
    {
        $this->setDateIntervals();

        $existGameInInterval = $this->entityManager->getRepository(Game::class)->checkExistOnDateInterval($this->startDate, $this->endDate);
        if ($existGameInInterval) {
            $this->setResponseMessage('Game was NOT created! Because on this days already exist game.');
        }
        else {
            $this->saveGame();
            $this->entityManager->getRepository(Prize::class)->resetAllWonPrizes();

            $this->setResponseMessage('Game was created!');
            return true;
        }
        return false;
    }

    private function setDateIntervals(): void
    {
        $this->startDate = \DateTime::createFromFormat('Y-m-d', $this->date);
        $this->endDate = $this->getEndDate($this->startDate);
    }

    private function getEndDate(\DateTimeInterface $startDate): \DateTimeInterface
    {
        $endDate = \DateTime::createFromInterface($startDate);
        $endDate->modify('+1 day');
        return $endDate;
    }

    private function saveGame(): void
    {
        $game = new Game();
        $game->setName($this->name);
        $game->setStartDate($this->startDate);
        $game->setEndDate($this->endDate);

        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    private function setResponseMessage(string $responseMessage): void
    {
        $this->responseMessage = $responseMessage;
    }

    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }
}