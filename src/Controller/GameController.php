<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\UserGamePrize;
use App\Form\UserPlayGameFormType;
use App\Service\GameService;
use App\Service\PlayGameRequirementsService;
use App\Service\PlayGameService;
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
        private GameService $gameService
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

    #[Route('/game', name: 'game_get_reward', methods: ['POST'])]
    public function getReward(
        Request $request,
        PlayGameService $playGameService
    ): Response
    {
        $response = [];
        $userGamePrize = new UserGamePrize();
        $form = $this->createForm(UserPlayGameFormType::class, $userGamePrize);
        $form->handleRequest($request);

        if ($this->gameRequirementsService->check() && $form->isSubmitted() && $form->isValid()) {
            if ($playGameService->play()) {
                $response = [
                    'status' => 'success',
                    'message' => 'You won prize !'
                ];
            }
            else {
                $response = [
                    'status' => 'fail',
                    'message' => $playGameService->getError()
                ];
            }
        }
        return $this->redirectToRoute('game', $response);
    }
}
