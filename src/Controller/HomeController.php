<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PublicationRepository $publicationRepository): Response
    {
        $publications = $publicationRepository->findBy(
            ['visibilite' => 'publique'],
            ['dateCreation' => 'DESC']
        );

        return $this->render('home/index.html.twig', [
            'publications' => $publications,
        ]);
    }
}