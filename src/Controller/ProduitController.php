<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CategorieRepository;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index')]
    public function index(ProduitRepository $produitRep, CategorieRepository $categorieRep): Response
    {
        $lesProduits = $produitRep -> findAll();
        $lesCategories = $categorieRep -> findAll();
        //dd($lesProduits);
        //dd($lesCategories);
        return $this->render('produit/index.html.twig', [
            'lesProduits' => $lesProduits,
            'lesCategories' => $lesCategories,
            'categorieActive' => null,
        ]);
    }

    #[Route('/categ/{id}', name: 'app_produit_indexCat')]
    public function indexCat(int $id, ProduitRepository $produitRep, CategorieRepository $categorieRep, ): Response
    {
        $lesProduits = $produitRep -> findBy(['uneCategorie' => $id]);
        $lesCategories = $categorieRep -> findAll();
        //dd($lesProduits);
        //dd($lesCategories);
        return $this->render('produit/index.html.twig', [
            'lesProduits' => $lesProduits,
            'lesCategories' => $lesCategories,
            'categorieActive' => $id,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]

    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]

    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]

    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
