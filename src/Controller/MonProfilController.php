<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PublicationRepository;

class MonProfilController extends AbstractController
{
    #[Route('/mon-profil', name: 'app_mon_profil')]
    public function profil(PublicationRepository $publicationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $publications = $publicationRepository->findBy(
            ['user' => $user],
            ['dateCreation' => 'DESC']
        );

        return $this->render('profil/monindex.html.twig', [
            'user' => $user,
            'publications' => $publications,
        ]);

    }
    /*==============================

         modifier mon profil

    ================================*/
   #[Route('/mon-profil/modifier', name: 'app_mon_profil_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été modifié.');

            return $this->redirectToRoute('app_mon_profil');
        }

        return $this->render('profil/modifier.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}