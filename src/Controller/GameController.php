<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGamePrize;
use App\Form\UserPlayGameFormType;
use App\Service\PlayGameRequirementsService;
use App\Service\PlayGameService;
use App\Utils\GameUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayGameRequirementsService $gameRequirementsService
    )
    {}

    #[Route('/game', name: 'game', methods: ['GET'])]
    public function index(): Response
    {
        $userCurrentGamePrize = $this->entityManager->getRepository(UserGamePrize::class)->getUserGamePrize($this->getUser());

        $userGamePrize = new UserGamePrize();
        $form = $this->createForm(UserPlayGameFormType::class, $userGamePrize);

        return $this->render('game/index.html.twig', [
            'playGameForm' => $form->createView(),
            'user_can_play' => $this->gameRequirementsService->check(),
            'error' => $this->gameRequirementsService->getError(),
            'user_current_game_prize' => $userCurrentGamePrize,
        ]);
    }

    #[Route('/game', name: 'game_get_reward', methods: ['POST'])]
    public function getReward(
        Request $request,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        $userGamePrize = new UserGamePrize();
        $form = $this->createForm(UserPlayGameFormType::class, $userGamePrize);
        $form->handleRequest($request);

        if ($this->gameRequirementsService->check() && $form->isSubmitted() && $form->isValid()) {
            $userGamePrizeData = $form->getData();
            dump($userGamePrizeData);

            //$playGameService->play();

            //return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }
        return $this->redirectToRoute('game');
    }
}
