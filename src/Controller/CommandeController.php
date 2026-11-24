<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommandeProduit;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande_index')]
    public function index(CommandeRepository $repoCommande): Response
    {
        // Non authentifié : accès refusé
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Admin : toutes les commandes. Sinon : uniquement celles de l'utilisateur connecté
        if ($this->isGranted('ROLE_ADMIN')) {
            $lesCdes = $repoCommande->findAll();
        } else {
            $lesCdes = $this->getUser()->getLesCommandes();
        }

        return $this->render('commande/index.html.twig', [
            'lesCdes' => $lesCdes,
        ]);
    }

    #[Route('/commande/add', name: 'app_commande_add', methods: ['POST'])]
    public function add(
        Request $request,
        ProduitRepository $produitRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('commande_add', $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (count($panier) === 0) {
            $this->addFlash('warning', 'Le panier est vide');
            return $this->redirectToRoute('app_panier_index');
        }

        $cde = new Commande();
        $cde->setDate(new \DateTime());
        $cde->setUnUtilisateur($this->getUser());

        $nbLignes = 0;
        foreach ($panier as $id => $quantite) {
            $produit = $produitRepo->find($id);
            if ($produit === null) {
                continue; // produit supprimé entre-temps
            }
            $ligne = new LigneCommandeProduit();
            $ligne->setUnProduit($produit);
            $ligne->setQuantite($quantite);
            $cde->addLesLignesCommandeProduit($ligne); // cascade persist
            $nbLignes++;
        }

        if ($nbLignes === 0) {
            $this->addFlash('warning', 'Aucun produit valide dans le panier');
            return $this->redirectToRoute('app_panier_index');
        }

        $entityManager->persist($cde);
        $entityManager->flush();

        // On vide le panier une fois la commande enregistrée
        $session->set('panier', []);
        $session->set('nbproduitpanier', 0);

        $this->addFlash('success', 'Votre commande a bien été enregistrée');
        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/commande/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detailCde(Commande $uneCde): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Un utilisateur ne peut voir que ses commandes (l'admin voit tout)
        if (!$this->isGranted('ROLE_ADMIN') && $uneCde->getUnUtilisateur()?->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas consulter cette commande.');
        }

        return $this->render('commande/detailcde.html.twig', [
            'uneCde' => $uneCde,
        ]);
    }
}
