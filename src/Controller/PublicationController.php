<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PublicationController extends AbstractController
{

    /*=========================
              Ajouter
    =========================*/
    #[Route('/publication/ajouter', name: 'app_publication_new')]
    public function ajouter (Request $request, EntityManagerInterface $entityManager): Response
    {
        // Seul un utilisateur connecté peut créer un article
        $this->denyAccessUnlessGranted('ROLE_USER');
         // Création d’un public vide
        $publication = new Publication();

        // Création du formulaire
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $publication->setUser($this->getUser());
            $publication->setDateCreation(new \DateTimeImmutable());

            $image = $form->get('image')->getData();

            if ($image instanceof UploadedFile) {
                $nomImage = uniqid() . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/publications',
                    $nomImage
                );

                $publication->setImage($nomImage);
            }

            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('app_feed');
        }

        return $this->render('publication/ajouter.html.twig', [
            'form' => $form,
        ]);
    }

    /*=========================
              Modifier
    =========================*/
    #[Route('/publication/{id}/modifier', name: 'app_publication_modifier')]
    public function modifier(
        Publication $publication,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($publication->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_feed');
        }

        return $this->render('publication/modifier.html.twig', [
            'form' => $form,
        ]);
    }
    /*=========================
              Supprimer
    =========================*/
    #[Route('/publication/{id}/supprimer', name: 'app_publication_supprimer', methods: ['POST'])]
    public function supprimer(
        Publication $publication,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($publication->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($publication);
        $entityManager->flush();

        return $this->redirectToRoute('app_feed');
    }
    /*=========================
              Afficher
    =========================*/
    #[Route('/publication/{id}', name: 'app_publication_afficher')]
    public function afficher(Publication $publication): Response
    {
        return $this->render('publication/afficher.html.twig', [
            'publication' => $publication,
        ]);
    }
}