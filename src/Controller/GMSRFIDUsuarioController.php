<?php

namespace App\Controller;

use App\Entity\GMSRFIDUsuario;
use App\Form\CambiarUsuarioType;
use App\Form\GMSRFIDUsuarioType;
use App\Repository\GMSRFIDUsuarioRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/g/m/s/r/f/i/d/usuario")
 */
class GMSRFIDUsuarioController extends AbstractController
{
    /**
     * @Route("/", name="g_m_s_r_f_i_d_usuario_index", methods={"GET"})
     */
    public function index(GMSRFIDUsuarioRepository $gMSRFIDUsuarioRepository): Response
    {
        return $this->render('gmsrfid_usuario/index.html.twig', [
            'g_m_s_r_f_i_d_usuarios' => $gMSRFIDUsuarioRepository->findByActivo(),
        ]);
    }

    /**
     * @Route("/new", name="g_m_s_r_f_i_d_usuario_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $gMSRFIDUsuario = new GMSRFIDUsuario();
        $form = $this->createForm(GMSRFIDUsuarioType::class, $gMSRFIDUsuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$gMSRFIDUsuario.setRol("ROLE_ADMIN");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gMSRFIDUsuario);
            $entityManager->flush();

            return $this->redirectToRoute('g_m_s_r_f_i_d_usuario_index');
        }

        return $this->render('gmsrfid_usuario/new.html.twig', [
            'g_m_s_r_f_i_d_usuario' => $gMSRFIDUsuario,
            'form' => $form->createView(),
        ]);
    }

    /*
     * @Route("/{id}", name="g_m_s_r_f_i_d_usuario_show", methods={"GET"})
     */
    /*public function show(GMSRFIDUsuario $gMSRFIDUsuario): Response
    {
        return $this->render('gmsrfid_usuario/show.html.twig', [
            'g_m_s_r_f_i_d_usuario' => $gMSRFIDUsuario,
        ]);
    }*/

    /**
     * @Route("/{id}/edit", name="g_m_s_r_f_i_d_usuario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GMSRFIDUsuario $gMSRFIDUsuario): Response
    {
        $form = $this->createForm(GMSRFIDUsuarioType::class, $gMSRFIDUsuario);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('g_m_s_r_f_i_d_usuario_index');
        }
        return $this->render('gmsrfid_usuario/edit.html.twig', [
            'g_m_s_r_f_i_d_usuario' => $gMSRFIDUsuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="g_m_s_r_f_i_d_usuario_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, GMSRFIDUsuario $gMSRFIDUsuario): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //if ($this->isCsrfTokenValid('delete'.$gMSRFIDUsuario->getId(), $request->request->get('_token'))) {
        $roles =$gMSRFIDUsuario->getRoles();
        if($roles[0] == 'ROLE_ADMIN' && $this->cantidadAdministradoresAplicacion() == 1){// no borrar si es el ultimo admonistrador
            echo "No se puede borrar el último administrador de la aplicaioón";
        }else{//borrar
            $entityManager = $this->getDoctrine()->getManager();
            $gMSRFIDUsuario->setEsActivo(false);
            //$entityManager->remove($gMSRFIDUsuario);
            $entityManager->flush();
        }
        //}

        return $this->redirectToRoute('g_m_s_r_f_i_d_usuario_index');
    }
    public function cantidadAdministradoresAplicacion(){
        $cant = 0;
        $usuariosRepository = $this->getDoctrine()->getRepository(GMSRFIDUsuario::class);
        $usuarios = $usuariosRepository->findAll();
        $r = null;
        foreach ($usuarios as $usuario){
            $r = $usuario->getRoles();
            if($r[0] == 'ROLE_ADMIN'){
                $cant++;
            }
        }
        return $cant;
    }

}
