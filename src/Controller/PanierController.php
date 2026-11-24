<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    private function nbProdPanier(): int
    {
        return array_sum($this->getSession()->get('panier', []));
    }

    private function majCompteur(): void
    {
        $this->getSession()->set('nbproduitpanier', $this->nbProdPanier());
    }

    #[Route('/panier', name: 'app_panier_index')]
    public function index(ProduitRepository $prodRepo): Response
    {
        // Le panier : tableau associatif [id du produit => quantité] stocké en session
        $panier = $this->getSession()->get('panier', []);
        $panierAvecDonnees = [];

        foreach ($panier as $id => $quantite) {
            $produit = $prodRepo->find($id);
            if ($produit) {
                $panierAvecDonnees[] = ['produit' => $produit, 'qte' => $quantite];
            }
        }

        $total = 0;
        foreach ($panierAvecDonnees as $item) {
            $total += (float) $item['produit']->getPrix() * $item['qte'];
        }

        $this->majCompteur();

        return $this->render('panier/index.html.twig', [
            'items' => $panierAvecDonnees,
            'total' => $total,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_panier_add', requirements: ['id' => '\d+'])]
    public function add(int $id): Response
    {
        $panier = $this->getSession()->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $this->getSession()->set('panier', $panier);
        $this->majCompteur();
        $this->addFlash('success', 'Produit ajouté au panier');

        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/panier/plus/{id}', name: 'app_panier_plus', requirements: ['id' => '\d+'])]
    public function plus(int $id): Response
    {
        $panier = $this->getSession()->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        }
        $this->getSession()->set('panier', $panier);
        $this->majCompteur();
        $this->addFlash('success', 'Vous avez ajouté une unité du produit');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/moins/{id}', name: 'app_panier_moins', requirements: ['id' => '\d+'])]
    public function moins(int $id): Response
    {
        $panier = $this->getSession()->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]--;
            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }
        $this->getSession()->set('panier', $panier);
        $this->majCompteur();
        $this->addFlash('success', 'Vous avez retiré une unité du produit');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/remove/{id}', name: 'app_panier_remove', requirements: ['id' => '\d+'])]
    public function remove(int $id): Response
    {
        $panier = $this->getSession()->get('panier', []);
        unset($panier[$id]);
        $this->getSession()->set('panier', $panier);
        $this->majCompteur();
        $this->addFlash('success', 'Produit supprimé du panier');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/panier/clear', name: 'app_panier_clear')]
    public function clear(): Response
    {
        $this->getSession()->set('panier', []);
        $this->getSession()->set('nbproduitpanier', 0);
        $this->addFlash('success', 'Panier vidé');

        return $this->redirectToRoute('app_panier_index');
    }
}
