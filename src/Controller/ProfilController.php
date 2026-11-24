<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordType;
use App\Form\ProfilPhotoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil_index')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'utilisateur' => $this->getUser(),
        ]);
    }

    #[Route('/profil/editPhoto', name: 'app_profil_editPhoto', methods: ['GET', 'POST'])]
    public function editPhoto(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Utilisateur $unUtil */
        $unUtil = $this->getUser();
        $form = $this->createForm(ProfilPhotoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $laPhoto = $form->get('photo')->getData();

            if ($laPhoto !== null) {
                $contenu = file_get_contents($laPhoto->getPathname());
                $unUtil->setPhoto('data:' . $laPhoto->getMimeType() . ';base64,' . base64_encode($contenu));
                $entityManager->flush();
                $this->addFlash('success', 'Votre photo a été modifiée');
            }

            return $this->redirectToRoute('app_profil_index');
        }

        return $this->render('profil/form.html.twig', [
            'titre' => 'Modifier ma photo',
            'form' => $form,
        ]);
    }

    #[Route('/profil/editmdp', name: 'app_profil_editmdp', methods: ['GET', 'POST'])]
    public function editMdp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var Utilisateur $unUtil */
        $unUtil = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        // L'ancien mot de passe est déjà vérifié par le formulaire (contrainte UserPassword)
        if ($form->isSubmitted() && $form->isValid()) {
            $unUtil->setPassword(
                $userPasswordHasher->hashPassword($unUtil, $form->get('newPassword')->getData())
            );
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe modifié, veuillez vous reconnecter');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/form.html.twig', [
            'titre' => 'Modifier mon mot de passe',
            'form' => $form,
        ]);
    }
}