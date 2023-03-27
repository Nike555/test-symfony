<?php

namespace App\Service;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {}

    public function getAllGames(): array
    {
        $games = $this->entityManager->getRepository(Game::class)->getAllGames();
        $data = [];

        if ($games) {
            foreach ($games as $game) {
                $data[] = $game->asArray();
            }
        }
        return $data;
    }
}