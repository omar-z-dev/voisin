<?php

namespace App\Controller;

use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AmiController extends AbstractController
{
    #[Route('/amis', name: 'app_amis')]
    public function index(
        FriendshipRepository $friendshipRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $amities = $friendshipRepository->findAcceptedFriendships($user);

        $utilisateurs = $userRepository->findAll();

        return $this->render('amis/index.html.twig', [
            'amities' => $amities,
            'utilisateurs' => $utilisateurs,
        ]);
    }

   #[Route('/amis/demande/{id}', name: 'app_ami_demande', methods: ['POST'])]
    public function demande(
        User $destinataire,
        FriendshipRepository $friendshipRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $demandeur = $this->getUser();

        if ($demandeur === $destinataire) {
            $this->addFlash('error', 'Vous ne pouvez pas vous ajouter vous-même.');

            return $this->redirectToRoute('app_profil', [
                'id' => $destinataire->getId(),
            ]);
        }

        $amitieExistante = $friendshipRepository->findFriendship(
            $demandeur,
            $destinataire
        );

        if ($amitieExistante) {
            $this->addFlash('error', 'Une demande d’amitié existe déjà.');

            return $this->redirectToRoute('app_profil', [
                'id' => $destinataire->getId(),
            ]);
        }

        $amitie = new Friendship();
        $amitie->setDemandeur($demandeur);
        $amitie->setDestinataire($destinataire);
        $amitie->setStatut('en_attente');
        $amitie->setDateCreation(new \DateTimeImmutable());

        $entityManager->persist($amitie);
        $entityManager->flush();

        $this->addFlash('success', 'Demande d’amitié envoyée.');

        return $this->redirectToRoute('app_profil', [
            'id' => $destinataire->getId(),
        ]);
    }
}