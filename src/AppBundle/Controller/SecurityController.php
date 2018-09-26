<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller{
    
    /**
     * @Route("/login_register", name="login_register")
     */
    public function DisplayLoginRegistrationPage(){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render(
            'login.html.twig',
            array('form' => $form->createView(),'error' => '','last_username' => '')
        );
    }

    /**
     * @Route("/loginAction", name="loginAction")
     */
    public function login(AuthenticationUtils $authenticationUtils){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/registerAction", name="registerAction")
     */
    public function registerAndLogin(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $token = new UsernamePasswordToken(
                $user,
                $password,
                'main',
                $user->getRoles()
            );
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main',serialize($token));

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            $this->addFlash('success','You are successufully registered');
            return $this->redirectToRoute('manage_list');
        }
        return $this->render(
            'login.html.twig',
            array('form' => $form->createView(),'error' => '','last_username' => '')
        );
    }

     /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
    }
}