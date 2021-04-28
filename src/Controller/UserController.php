<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use App\Service\UsersService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $encoder;
    private $urlGenerator;

    public function __construct(UserPasswordEncoderInterface $encoder, UrlGeneratorInterface $urlGenerator)
    {
        $this->encoder = $encoder;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/registration", name="app_register")
     *
     * @param Request $request
     * @param UsersService $usersService
     * @return Response
     * @throws Exception
     */
    public function registration(Request $request, UsersService $usersService): Response
    {
        $form = $this->createForm(UserRegistrationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $form['plainPassword']->getData()));
            $user->setToken(md5(random_bytes(10)));
            $usersService->save($user);
            $usersService->sendRegistrationEmail($user);
            $this->addFlash('success', 'Your account has been register, please check your emails to activate it !');

            return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/activate/{username}/{token}", name="user_activate")
     *
     * @param UsersService $usersService
     * @param UserRepository $repository
     * @param $username
     * @param $token
     *
     * @return RedirectResponse
     */
    public function userActivate(UsersService $usersService, UserRepository $repository, $username, $token)
    {
        $user = $repository->findOneBy(['username' => $username, 'token' => $token]);
        if ($user && $token && $user->getToken() === $token) {
            $user->setToken(null);
            $user->setActivated(true);
            $usersService->save($user);
            $this->addFlash('success', 'Your account has been activated !');
        } else {
            error_log('Account activation failed !');
            $this->addFlash('danger', 'Account activation failed !');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    /**
     * @Route("/forgot_password", name="forgot_password")
     *
     * @param Request $request
     * @param UserRepository $repository
     * @param UsersService $usersService
     * @return Response
     *
     * @throws Exception
     */
    public function forgotPassword(Request $request, UserRepository $repository, UsersService $usersService)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form['username']->getData();
            $user = $repository->findOneBy(['username' => $username]);
            if (!$user) {
                $this->addFlash('danger', 'This user do not exist !');
            } else {
                $user->setToken(md5(random_bytes(10)));
                $usersService->save($user);
                $usersService->sendPasswordResetEmail($user);
                $this->addFlash('success', 'A password reset email has been sent for your account !');
            }
        }

        return $this->render('user/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/reset_password/{username}/{token}", name="reset_password")
     *
     * @param UserRepository $repository
     * @param $username
     * @param Request $request
     * @param $token
     *
     * @param UsersService $usersService
     * @return Response
     */
    public function resetPassword(UserRepository $repository, $username, Request $request, $token, UsersService $usersService)
    {
        $user = $repository->findOneBy(['username' => $username, 'token' => $token]);
        if (!$user) {
            return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
        }
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setPassword($this->encoder->encodePassword($user, $form['plainPassword']->getData()));
            $usersService->save($user);
            $this->addFlash('success', 'Password updated !');

            return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
        }

        return $this->render('user/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
