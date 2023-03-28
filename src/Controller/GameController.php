<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\UserGamePrize;
use App\Service\CreateGameService;
use App\Service\GameService;
use App\Service\PlayGameRequirementsService;
use App\Service\PlayGameService;
use App\Service\UserGamePrizeService;
use App\Utils\GameUtils;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class GameController extends AbstractFOSRestController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayGameRequirementsService $gameRequirementsService,
        private GameService $gameService,
        private UserGamePrizeService $userGamePrizeService
    )
    {}

    #[Route('/games', name: 'store_game', methods: ['POST'])]
    public function store(Request $request, CreateGameService $createGameService): View
    {
        $name = $request->get('name');
        $date = $request->get('date');

        $createGameService->setName($name);
        $createGameService->setDate($date);
        if ($createGameService->create()) {
            $response['status'] = Response::HTTP_CREATED;
        }
        else {
            $response['status'] = Response::HTTP_BAD_REQUEST;
        }
        $response['message'] = $createGameService->getResponseMessage();

        return $this->view($response, $response['status']);
    }

    #[Route('/games', name: 'get_games', methods: ['GET'])]
    public function index(): View
    {
        $games = $this->gameService->getAllGames();

        if (!$games) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => 'Games not found',
            ];
            return $this->view($data, $data['status']);
        }
        return $this->view($games, Response::HTTP_OK);
    }

    #[Route('/games/current', name: 'get_current_game', methods: ['GET'])]
    public function currentGame(): View
    {
        $currentGame = $this->gameService->getCurrentGame();

        if (!$currentGame) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => 'Game not found',
            ];
            return $this->view($data, $data['status']);
        }
        return $this->view($currentGame, Response::HTTP_OK);
    }

    #[Route('/games/play', name: 'game_play', methods: ['GET'])]
    public function getReward(PlayGameService $playGameService): View
    {
        if ($this->gameRequirementsService->check()) {
            if ($playGameService->play()) {
                $userCurrentGamePrize = $this->userGamePrizeService->getUserGamePrize($this->getUser());

                $response = [
                    'status' => Response::HTTP_OK,
                    'message' => 'You won prize !',
                    'prize_info' => $userCurrentGamePrize
                ];
            }
            else {
                $response = [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => $playGameService->getError(),
                ];
            }
        }
        else {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $this->gameRequirementsService->getError(),
            ];

            $oldUserPrize = $this->userGamePrizeService->getUserGamePrize($this->getUser());
            if ($oldUserPrize) {
                $response['old_prize_info'] = $oldUserPrize;
            }
        }
        return $this->view($response, $response['status']);
    }
}
