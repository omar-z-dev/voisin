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
}