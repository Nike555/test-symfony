<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGamePrize;
use App\Form\UserPlayGameFormType;
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
        private EntityManagerInterface $entityManager
    )
    {}

    #[Route('/game', name: 'game', methods: ['GET'])]
    public function index(): Response
    {
        $userCanPlay = $this->userCanPlay();
        $userCurrentGamePrize = $this->entityManager->getRepository(UserGamePrize::class)->getUserGamePrize($this->getUser());
        $userGamePrize = new UserGamePrize();
        $form = $this->createForm(UserPlayGameFormType::class, $userGamePrize);

        return $this->render('game/index.html.twig', [
            'playGameForm' => $form->createView(),
            'user_can_play' => $userCanPlay,
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

        if ($this->userCanPlay() && $form->isSubmitted() && $form->isValid()) {
            $userGamePrizeData = $form->getData();
            dump($userGamePrizeData);

            //$eventDispatcher->dispatch(new CommentCreatedEvent($userGamePrize));

            //return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }
        return $this->redirectToRoute('game');
    }

    /**
     * Check if current user can play game
     * @return bool
     */
    private function userCanPlay(): bool
    {
        return $this->entityManager->getRepository(UserGamePrize::class)->checkUserCanPlay($this->getUser());
    }
}
