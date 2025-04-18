<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Entity\MessageContact;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class ContactController extends AbstractController{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        $message = new MessageContact();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new \DateTimeImmutable());
            $em->persist($message);
            $em->flush();
    
            // Envoi de l'email
            $email = (new Email())
            ->from('contact@cameleon-solutions.fr') // ✅ l’adresse qui t’appartient
            ->replyTo($message->getEmail())         // ✅ pour que tu puisses répondre à la personne
            ->to('contact@cameleon-solutions.fr')
            ->subject('Nouveau message de contact')
            ->html("<p><strong>Nom :</strong> {$message->getName()}</p>
                    <p><strong>Email :</strong> {$message->getEmail()}</p>
                    <p><strong>Message :</strong><br>{$message->getMessage()}</p>");
        
    
            $mailer->send($email);
    
            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_contact');
        }
    
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
