<?php

namespace App\Controller;

use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $series = $this->getDoctrine()->getRepository(Series::class)->findRanked();
        $seriesUnranked = $this->getDoctrine()->getRepository(Series::class)->findUnranked();

        return $this->render('series/index.html.twig', [
            'series' => $series,
            'seriesUnranked' => $seriesUnranked,
        ]);
    }
}
