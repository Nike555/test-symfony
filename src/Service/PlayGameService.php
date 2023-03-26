<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Prize;
use App\Entity\UserGamePrize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PlayGameService
{
    private $error = '';
    private $currentDay;
    private $currentUser;

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private PlayGameRequirementsService $gameRequirementsService
    )
    {
        $this->currentDay = (new \DateTime())->format('Y-m-d');
        $this->currentUser = $this->security->getUser();
    }

    public function play()
    {
        $userCanPlay = $this->gameRequirementsService->check();
        if ($userCanPlay) {
            return $this->getUserPrize();
        }
        else {
            $this->error = $this->gameRequirementsService->getError();
        }
        return false;
    }

    private function getUserPrize(): bool
    {
        $currentGame = $this->entityManager->getRepository(Game::class)->getCurrentDayGame();
        $randomPrize = $this->entityManager->getRepository(Prize::class)->getPrizeWithoutWins($this->currentUser);

        if ($currentGame) {
            $userGamePrize = new UserGamePrize();
            $userGamePrize->setGame($currentGame);
            $userGamePrize->setUser($this->currentUser);
            $userGamePrize->setPrize($randomPrize);
            $userGamePrize->setDate(new \DateTime());

            $this->entityManager->persist($userGamePrize);
            $this->entityManager->flush();

            // Set prize as won
            $this->entityManager->getRepository(Prize::class)->setWonPrize($randomPrize->getUniqueCode());
            return true;
        }
    }

    public function getError(): string
    {
        return $this->error;
    }
}