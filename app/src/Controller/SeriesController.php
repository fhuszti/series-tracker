<?php

namespace App\Controller;

use App\Entity\Series;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
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

    /**
     * @Route("/series/{id}", name="update_series", methods={"PUT"})
     * @throws Exception
     */
    public function update(Series $series, Request $request): JsonResponse
    {
        $content = json_decode($request->getContent());

        try {
            //les données contiennent en clé le nom de la méthode qui doit être appelée
            foreach ($content as $key => $data) {
                if (method_exists(Series::class, $key))
                    $series->$key($data);
                else
                    throw new Exception("La méthode ".$key." n'existe pas dans l'entité Series", 400);
            }
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        return new JsonResponse("La série '".$series->getTitle()."' a été mise à jour", 200);
    }
}
