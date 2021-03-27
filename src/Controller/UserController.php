<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $encoder;
    private $urlGenerator;
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $encoder, UrlGeneratorInterface $urlGenerator, MailerInterface $mailer)
    {
        $this->encoder = $encoder;
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/registration", name="app_register")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function registration(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(UserRegistrationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $form['plainPassword']->getData()));
            $user->setToken(md5(random_bytes(10)));
            $em->persist($user);
            $em->flush();
            self::sendRegistrationEmail($user);
            $this->addFlash('success', 'Your account has been register, please check your emails to activate it !');
            return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function sendRegistrationEmail(User $user)
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@snowtricks.com')
            ->to($user->getEmail())
            ->subject('Validation of your SnowTricks account')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'user' => $user
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @Route("/user/activate/{username}/{token}", name="user_activate")
     * @param EntityManagerInterface $em
     * @param UserRepository $repository
     * @param $username
     * @param $token
     * @return RedirectResponse
     */
    public function userActivate(EntityManagerInterface $em, UserRepository $repository, $username, $token)
    {
        $user = $repository->findOneBy(['username' => $username]);
        if ($user && $token && $user->getToken() === $token)
        {
            $user->setActivated(true);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your account has been activated !');
        }
        else
        {
            error_log('Account activation failed !');
            $this->addFlash('danger', 'Account activation failed !');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
