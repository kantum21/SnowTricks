<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class UsersService
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    public function save(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function sendRegistrationEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@snowtricks.com')
            ->to($user->getEmail())
            ->subject('Validation of your SnowTricks account')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }
    }

    public function sendPasswordResetEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@snowtricks.com')
            ->to($user->getEmail())
            ->subject('Reset password for your SnowTricks account')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }
    }
}
