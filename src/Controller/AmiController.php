<?php

namespace App\Controller;

use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AmiController extends AbstractController
{
    #[Route('/amis', name: 'app_amis')]
    public function index(FriendshipRepository $friendshipRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $amities = $friendshipRepository->findAcceptedFriendships($user);

        return $this->render('amis/index.html.twig', [
            'amities' => $amities,
        ]);
    }
}