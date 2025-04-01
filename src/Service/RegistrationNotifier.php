<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class RegistrationNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    public function sendNotification(User $user): void
    {
        $email = (new Email())
            ->from('noreply@cameleon-solutions.fr')
            ->to('contact@cameleon-solutions.fr') // ton adresse de réception
            ->subject('Nouvelle inscription sur Cameleon Solutions Learning')
            ->html($this->twig->render('emails/registration_notification.html.twig', [
                'user' => $user
            ]));

        $this->mailer->send($email);
    }
}
