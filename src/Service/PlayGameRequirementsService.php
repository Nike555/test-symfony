<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Prize;
use App\Entity\UserGamePrize;
use App\Utils\GameUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

class PlayGameRequirementsService
{
    private $error;
    private $currentUser;

    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    )
    {
        $this->currentUser = $this->security->getUser();
    }

    public function check(): bool
    {
        if ($this->checkTime() && !$this->checkPlayedThisGame() && $this->checkAvailablePrizes()) {
            return true;
        }
        return false;
    }

    private function checkTime(): bool
    {
        return true;
        $correctTimeInterval = GameUtils::checkPlayTimeInterval();
        if (!$correctTimeInterval) {
            $this->setError('You can\'t play in this time interval!');
            return false;
        }
        return true;
    }

    /**
     * Check if used played game also if was played current day
     * @return bool
     */
    private function checkPlayedThisGame(): bool
    {
        $playedThisGame = $this->entityManager->getRepository(UserGamePrize::class)->checkUserPlayedGame($this->currentUser);

        if ($playedThisGame) {
            $currentDay = (new \DateTime())->format('Y-m-d');
            $todayWasPlayed = false;
            foreach ($playedThisGame as $gameDays) {
                if ($gameDays->getDate()->format('Y-m-d') === $currentDay) {
                    $todayWasPlayed = true;
                }
            }
            if ($todayWasPlayed) {
                $this->setError('You already played this game, please wait another game.');
                return true;
            }
        }
        return false;
    }

    private function checkAvailablePrizes(): bool
    {
        $userLanguageId = $this->currentUser->getLanguage()->getId();

        $gameCountDays = $this->entityManager->getRepository(Game::class)->currentGameCountDays();
        $totalPrizes = $this->entityManager->getRepository(Prize::class)->totalPrizesByUserLanguage($userLanguageId);
        $countAvailablePrizesPerDay = floor($totalPrizes / $gameCountDays);
        $countPrizesWonToday = $this->entityManager->getRepository(UserGamePrize::class)->getCountWonTodayPrizes();
        $availablePrizes = $countAvailablePrizesPerDay - $countPrizesWonToday;

        if (!$availablePrizes) {
            $this->setError('Sorry, all prizes per today was won.');
        }
        return true;
    }

    private function setError(string $error)
    {
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}