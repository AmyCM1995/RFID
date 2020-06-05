<?php


namespace App\Controller;


use App\Entity\HistorialLectores;
use App\Entity\IPLectorCubano;
use App\Entity\Lector;
use App\Repository\LectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/traceroute")
 */
class TracerouteController extends AbstractController
{
    /**
     * @Route("/{id}/manual", name="traceroute_manual", methods={"GET"})
     */
    public function manual(Lector $lector): Response
    {
        $ip = $this->getDoctrine()->getRepository(IPLectorCubano::class)->findOneByIdLector($lector->getId());
        $tracert = "tracert -d ".$ip->getIp();
        exec($tracert, $output);
        //guardar en el historial
        $resultado = $this->convertirOutputEnResultado($output);
        $historialLector = new HistorialLectores();
        $historialLector->setResultado($resultado);
        $historialLector->setIpLector($ip);
        $historialLector->setFechaHora(new \DateTime('now'));
        $historialLector->setEstado("incognita");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($historialLector);
        $entityManager->flush();
        return $this->render('traceroute/manual.html.twig', [
            'output' => $output,
        ]);
    }
    /**
     * @Route("/automatico", name="traceroute_automatico", methods={"GET"})
     */
    public function automatico(): Response
    {

        $ips = $this->getDoctrine()->getRepository(IPLectorCubano::class)->findAll();
        foreach ($ips as $ip){
            $tracert = "tracert -d ".$ip->getIp();
            exec($tracert, $output);
            //guardar en el historial
            $resultado = $this->convertirOutputEnResultado($output);
            $historialLector = new HistorialLectores();
            $historialLector->setResultado($resultado);
            $historialLector->setIpLector($ip);
            $historialLector->setFechaHora(new \DateTime('now'));
            $historialLector->setEstado("incognita");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($historialLector);
            $entityManager->flush();
        }
    }
    /**
     * @Route("/", name="traceroute_index", methods={"GET"})
     */
    public function index(LectorRepository $lectorRepository): Response
    {
        //solo muestro los lectors a los que se le puede hacer traceroute, o sea los que tienen definido el ip
        $IPlectores = $this->getDoctrine()->getRepository(IPLectorCubano::class)->findAll();
        $lectores[] = null;
        $size = 0;
        foreach ($IPlectores as $ip){
            $lectores[$size] = $lectorRepository->findOneByIpLectores($ip->getLector()->getId());
            $size++;
        }

        return $this->render('traceroute/index.html.twig', [
            'lectors' => $lectores,
        ]);
    }
    public function convertirOutputEnResultado($output){
        $resultado = $output[2];
        $s = null;
        for ($i=3; $i<sizeof($output)-1; $i++){
            $s = $output[$i];
            $resultado = $resultado.$s;
        }
        $resultado = utf8_encode($resultado);
        return $resultado;
    }
}