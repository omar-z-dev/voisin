<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\UserRepository;


final class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    #[IsGranted('ROLE_USER')]
    public function index(PublicationRepository $publicationRepository): Response
    {
        $publications = $publicationRepository->findBy(
            [],
            ['dateCreation' => 'DESC']
        );

        return $this->render('feed/index.html.twig', [
            'user' => $this->getUser(),
            'publications' => $publications,
        ]);
    }
}