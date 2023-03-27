<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractFOSRestController
{
    #[Route('/api/auth/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): View
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $languageName = $request->get('language');
        $language = $entityManager->getRepository(Language::class)->findOneBy(['name' => $languageName]);

        $user = $entityManager->getRepository(User::class)->existByEmail($email);

        if ($user) {
            return $this->view(['message' => 'User already exist'], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($userPasswordHasher->hashPassword($user,$password));
        $user->setLanguage($language);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->view($user, Response::HTTP_CREATED)->setContext((new Context())->setGroups(['public']));
    }
}
