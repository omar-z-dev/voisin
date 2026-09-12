<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil')]
    public function profil(
        User $user,
        PublicationRepository $publicationRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $publications = $publicationRepository->findBy(
            ['user' => $user],
            ['dateCreation' => 'DESC']
        );

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'publications' => $publications,
        ]);
    }
}