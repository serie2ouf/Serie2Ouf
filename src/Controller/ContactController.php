<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact_index')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $unContact = new Contact();
        $form = $this->createForm(ContactType::class, $unContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($unContact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été enregistrée. Nous vous répondrons rapidement');

            // EMAIL de confirmation
            $mail = (new Email())
                ->subject("Demande d'informations")
                ->from(new Address('noreply@noreply.fr', 'Elec Mob'))
                ->to($unContact->getEmail())
                ->html($this->renderView('contact/mail.html.twig', [
                    'unContact' => $unContact,
                ]), 'text/html');

            try {
                $mailer->send($mail);
                $this->addFlash('success', 'Email de confirmation envoyé avec succès');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('warning', "Un problème a eu lieu durant l'envoi de l'email, veuillez ré-essayer plus tard");
            }

            return $this->redirectToRoute('app_contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $form->createView(),
        ]);
    }
}
