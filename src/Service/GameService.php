<?php

namespace App\Service;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    private GameRepository $gameRepository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
        $this->gameRepository = $this->entityManager->getRepository(Game::class);
    }

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
}