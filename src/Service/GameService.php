<?php

namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    public function __construct(
        private GameRepository $gameRepository
    )
    {}

    public function getAllGames(): array
    {
        $games = $this->gameRepository->getAllGames();
        $data = [];

        if ($games) {
            foreach ($games as $game) {
                $data[] = $game->asArray();
            }
        }
        return $data;
    }

    public function getCurrentGame(): ?array
    {
        $game = $this->gameRepository->getCurrentDayGame();

        if ($game instanceof Game) {
            return $game->asArray();
        }
        return null;
    }
}