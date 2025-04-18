<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $password = $form->get('plainPassword')->getData();
            $confirm  = $form->get('plainPasswordConfirm')->getData();
        
            if ($password !== $confirm) {
                $form->get('plainPasswordConfirm')->addError(new FormError('Les mots de passe ne correspondent pas.'));
            }
        }
        

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Enregistre le nouvel utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoie l'email de confirmation
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@cameleon-solutions.fr', 'Caméléon Solutions Learning'))
                    ->to($user->getEmail())
                    ->subject('Confirmez votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Envoie une notification à l'admin
            $adminNotification = (new TemplatedEmail())
                ->from(new Address('contact@cameleon-solutions.fr', 'Caméléon Solutions Learning'))
                ->to('contact@cameleon-solutions.fr') // 👈 ou ton email admin
                ->subject('Nouvelle inscription sur Caméléon Learning')
                ->htmlTemplate('emails/new_registration.html.twig')
                ->context([
                    'user' => $user,
                    'formationsSouhaitees' => $form->get('formationsSouhaitees')->getData() ?? null,
                ]);
            $mailer->send($adminNotification);

            // Connexion automatique après l'inscription
            $this->addFlash('success', 'Merci pour votre inscription. Un email de confirmation vous a été envoyé. Veuillez vérifier dans vos spams si vous ne le voyez pas.');
            return $this->redirectToRoute('app_login');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans(
                $exception->getReason(),
                [],
                'VerifyEmailBundle'
            ));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse email a bien été confirmée.');
        return $this->redirectToRoute('app_formations');
    }

    
}
