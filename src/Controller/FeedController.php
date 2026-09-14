<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FriendshipRepository;

final class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        PublicationRepository $publicationRepository,
        FriendshipRepository $friendshipRepository
    ): Response {

        //récupère l'utilisateur actuellement connecté.
        $user = $this->getUser();

        //récupérer les amis de l'utilisateur connecté , $amities contient des objets Friendship
        $amities = $friendshipRepository->findAcceptedFriendships($user);

        //récupérer le tableau de tous les amis de l'utilisateur connecté
        $amis = [];
       
        foreach ($amities as $amitie) {
            if ($amitie->getDemandeur() === $user) {
                $amis[] = $amitie->getDestinataire();
            } else {
                $amis[] = $amitie->getDemandeur();
            }
        }

        $filtre = $request->query->get('filtre', 'toutes');

        //Si filtre = 'toutes', on affiche toutes les publications
        $publications = $publicationRepository->findBy(
            [],
            ['dateCreation' => 'DESC']
        );

        if ($filtre === 'publiques') {
            $publications = array_filter(
                $publications,
                fn($publication) => $publication->getVisibilite() === 'publique'
            );
        }

        if ($filtre === 'amis') {

            $publications = array_filter(
                $publications,
                fn($publication) =>
                    $publication->getVisibilite() === 'amis'
                    //Est-ce que l'auteur de cette publication est dans la liste de mes amis ?
                    && in_array($publication->getUser(), $amis, true)
            );
        }

        return $this->render('feed/index.html.twig', [
            'user' => $this->getUser(),
            'publications' => $publications,
            'filtre' => $filtre,
        ]);
    }
}