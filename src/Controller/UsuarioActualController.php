<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class UsuarioActualController extends AbstractController
{
    /**
     * @Route("/usuario/actual", name="usuario_actual")
     */
    public function index()
    {
        return $this->render('usuario_actual/index.html.twig', [
            'controller_name' => 'UsuarioActualController',
        ]);
    }
    public function accionesGenerales(){
        $user = $this->getUser();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return new Response($user.getNombre());
    }
}
