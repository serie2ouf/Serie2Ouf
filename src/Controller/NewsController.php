<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\NewsType;


final class NewsController extends AbstractController
{
    #[Route('/', name: 'app_news_index')]
    public function index(NewsRepository $newsRepo): Response
    {
        $lesNews = $newsRepo->findAll();
        return $this->render('news/index.html.twig', [
            'lesNews' => $lesNews,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/news/gestion', name: 'app_news_gestion')]
    public function gestion(NewsRepository $newsRepo): Response
    {
        $lesNews = $newsRepo->findAll();
        return $this->render('news/gestion.html.twig', [
            'lesNews' => $lesNews,
        ]);
    }

    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/news/show/{id}', name: 'app_news_show')]
    public function show(News $uneNews): Response
    {
        return $this->render('news/show.html.twig', [
            'uneNews' => $uneNews,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/news/new', name: 'app_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uneNews = new News();
        $form = $this->createForm(NewsType::class, $uneNews);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($uneNews);
            $entityManager->flush();

            return $this->redirectToRoute('app_news_gestion');
        }

        return $this->render('news/new.html.twig', [
            'formNews' => $form->createView(),
            'uneNews' => $uneNews
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/news/edit/{id}', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(News $uneNews, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $uneNews);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($uneNews);
            $entityManager->flush();

            return $this->redirectToRoute('app_news_gestion');
        }

        return $this->render('news/edit.html.twig', [
            'formNews' => $form->createView(),
            'uneNews' => $uneNews
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/news/delete/{id}', name: 'app_news_delete', methods: ['GET', 'POST'])]
    public function delete(News $uneNews, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('delete' . $uneNews->getId(), $request->request->get('_token'))) {
                $entityManager->remove($uneNews);
                $entityManager->flush();
                return $this->redirectToRoute('app_news_gestion');
            }
        }
        return $this->render('news/delete.html.twig', ['uneNews' => $uneNews]);
    }
}

