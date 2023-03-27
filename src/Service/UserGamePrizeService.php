<?php

namespace App\Service;

use App\Entity\UserGamePrize;
use App\Repository\UserGamePrizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserGamePrizeService
{
    private UserGamePrizeRepository $userGamePrizeRepository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
        $this->userGamePrizeRepository = $this->entityManager->getRepository(UserGamePrize::class);
    }

    public function getUserGamePrize(UserInterface $user): ?array
    {
        $prize = $this->userGamePrizeRepository->getUserGamePrize($user);

        if ($prize instanceof UserGamePrize) {
            return [
                'game_name' => $prize->getGame()->getName(),
                'prize_name' => $prize->getPrize()->getName(),
                'partner_name' => $prize->partner->getName(),
                'partner_url' => $prize->partner->getUrl(),
            ];
        }
        return null;
    }
}