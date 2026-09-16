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
    /*==========================
               INDEX       
    ==========================*/
    #[Route('/amis', name: 'app_amis')]
    public function index(
        FriendshipRepository $friendshipRepository,
        UserRepository $userRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        // Récupérer les amis de l'utilisateur connecté
        $amities = $friendshipRepository->findAcceptedFriendships($user);

        // Récupérer les demandes d'ami en attente de l'utilisateur connecté
        $demandes = $friendshipRepository->findBy([
            'destinataire' => $user,
            'statut' => 'en_attente'
        ]);

        // Récupérer tous les utilisateurs de l'application
        $utilisateurs = $userRepository->findAll();

        return $this->render('amis/index.html.twig', [
            'amities' => $amities,
            'utilisateurs' => $utilisateurs,
            'demandes' => $demandes,
            'user' => $user
        ]);
    }

    /*=============================

             DEMANDE D AMI   

    ==============================*/
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
            $demandeur, $destinataire);

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
    /*=======================================

             ACCEPTER UNE DEMANDE D AMI  

    ========================================*/
   #[Route('/amis/accepter/{id}', name: 'app_ami_accepter', methods: ['POST'])]
    public function accepter(
        Friendship $amitie,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($amitie->getDestinataire() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $amitie->setStatut('acceptee');
        $entityManager->flush();

        return $this->redirectToRoute('app_amis');
    }
    /*=======================================

             REFUSER UNE DEMANDE D AMI  

    ========================================*/
   #[Route('/amis/refuser/{id}', name: 'app_ami_refuser', methods: ['POST'])]
    public function refuser(
        Friendship $amitie,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($amitie->getDestinataire() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($amitie);
        $entityManager->flush();

        return $this->redirectToRoute('app_amis');
    }
    /*==================================================

             AFFICHER LES DEMANDES D'AMI

    ====================================================*/
    #[Route('/amis/demandes', name: 'app_demandes')]
    public function demandes(
        FriendshipRepository $friendshipRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $demandes = $friendshipRepository->findBy([
            'destinataire' => $user,
            'statut' => 'en_attente'
        ]);

        return $this->render('amis/demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }
}