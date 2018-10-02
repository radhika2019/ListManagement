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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends Controller{
    
    /**
     * @Route("/", name="login_register")
     */
    public function DisplayLoginRegistrationPage(AuthorizationCheckerInterface $authChecker){

        if (true === $authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('manage_list');
        }
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
     * @Route("confirmation", name="confirmation")
     */
    public function emailConfirmationAndRegistration(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder){

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $data = $form->getData();
        $email = $data->getEmail();
        $username = $data->getUsername();
        if($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = (new \Swift_Message('Please verify your email address'))
            ->setFrom('rchoudhary16108@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'confirmation/confirmation.txt.twig',
                    array('username' => $username)
                ),
                'text/html'
            );
            $mailer->send($message);
            return $this->render('confirmation/message.html.twig',array('username' => $username,'email' => $email));
        }

        return $this->render(
            'login.html.twig',
            array('form' => $form->createView(),'error' => '','last_username' => '')
        );
    }

    /**
     * @Route("/activation/{username}", name="activation")
     */
    public function accountActivation($username){
        
       $entityManager = $this->getDoctrine()->getManager();
       $user = $entityManager->getRepository(User::class)->findOneBy([
            'username' => $username]);
       $user->setIsActive(1);
       $em = $this->getDoctrine()->getManager();
       $entityManager->flush();
       return $this->render('confirmation/welcome.html.twig');
    }

     /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }
}