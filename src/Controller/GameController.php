<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\UserGamePrize;
use App\Form\UserPlayGameFormType;
use App\Service\GameService;
use App\Service\PlayGameRequirementsService;
use App\Service\PlayGameService;
use App\Service\UserGamePrizeService;
use App\Utils\GameUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class GameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayGameRequirementsService $gameRequirementsService,
        private GameService $gameService,
        private UserGamePrizeService $userGamePrizeService
    )
    {}

    #[Route('/game', name: 'game', methods: ['GET'])]
    public function index2(Request $request): Response
    {
        $userCurrentGamePrize = $this->entityManager->getRepository(UserGamePrize::class)->getUserGamePrize($this->getUser());
        $playGame['status'] = $request->query->get('status');
        $playGame['message'] = $request->query->get('message');

        $userGamePrize = new UserGamePrize();
        $form = $this->createForm(UserPlayGameFormType::class, $userGamePrize);

        return $this->render('game/index.html.twig', [
            'playGameForm' => $form->createView(),
            'user_can_play' => $this->gameRequirementsService->check(),
            'error' => $this->gameRequirementsService->getError(),
            'user_current_game_prize' => $userCurrentGamePrize,
            'play_game' => $playGame,
        ]);
    }

    #[Route('/games', name: 'get_games', methods: ['GET'])]
    public function index(): Response
    {
        $games = $this->gameService->getAllGames();

        if (!$games) {
            $data = [
                'status' => 404,
                'errors' => 'Games not found',
            ];
            return $this->json($data, $data['status']);
        }
        return $this->json($games);
    }

    #[Route('/games/current', name: 'get_current_game', methods: ['GET'])]
    public function currentGame(Request $request): Response
    {
        $currentGame = $this->gameService->getCurrentGame();

        if (!$currentGame) {
            $data = [
                'status' => 404,
                'errors' => 'Game not found',
            ];
            return $this->json($data, $data['status']);
        }
        return $this->json($currentGame);
    }

    #[Route('/games/play', name: 'game_play', methods: ['GET'])]
    public function getReward(
        Request $request,
        PlayGameService $playGameService
    ): Response
    {
        $userCurrentGamePrize = $this->userGamePrizeService->getUserGamePrize($this->getUser());
        if ($this->gameRequirementsService->check()) {
            if ($playGameService->play()) {
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
                'old_prize_info' => $userCurrentGamePrize
            ];
        }
        return $this->json($response, $response['status']);
    }
}
