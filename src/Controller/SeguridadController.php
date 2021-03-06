<?php

namespace App\Controller;

use App\Entity\GMSRFIDUsuario;
use App\Form\CambiarUsuarioType;
use App\Repository\GMSRFIDUsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SeguridadController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/{nombreUsuario}/cambiar/contrasena", name="user_contrasena_cambiar", methods={"GET","POST"})
     */
    public function cambiarContrasena(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserInterface $user){

        $form = $this->createForm(CambiarUsuarioType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Su contraseña ha cambiado correctamente. Por favor inicie sesión nuevamente");
            $url = $this->generateUrl('app_login');
            $response = new RedirectResponse($url);
            return $response;
        }
        return $this->render('security/forgot.html.twig', array('form' => $form->createView()));
    }

}
