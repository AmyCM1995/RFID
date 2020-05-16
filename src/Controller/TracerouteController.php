<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/traceroute")
 */
class TracerouteController extends AbstractController
{
    /**
     * @Route("/ping", name="traceroute_ping", methods={"GET"})
     */
    public function index(): Response
    {
        exec("tracert -d 192.168.1.2", $output);
        foreach ($output as $x){
            print_r($x."<br>");
        }
        return $this->render('traceroute/ping.html.twig', [
            'output' => $output,
        ]);
    }

}