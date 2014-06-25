<?php

namespace Soundvenirs\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Soundvenirs\SoundBundle\Factory;

class SoundsController extends Controller
{
    public function retrieveOneAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SoundvenirsSoundBundle:Sound');
        $sound = $repo->find($id);

        if ($sound === null) {
            return new JsonResponse(null, 404);
        }

        $responseSound = [];
        $responseSound['id'] = $sound->id;
        $responseSound['title'] = $sound->title;
        $responseSound['mp3url'] = 'http://www.soundvenirs.com/download/'.$sound->id.'.mp3';
        $responseSound['location'] = [
            'lat' => $sound->lat,
            'long' => $sound->long
        ];
        return new JsonResponse($responseSound);
    }

    public function createAction()
    {
        $content = $this->get('request')->getContent();
        $params = json_decode($content, true);
        $title = $params['title'];

        $repo = $this->getDoctrine()->getManager()->getRepository('SoundvenirsSoundBundle:Sound');
        $soundFactory = new Factory\Sound($repo);
        $sound = $soundFactory->create();
        $sound->title = $title;

        $em = $this->getDoctrine()->getManager();
        $em->persist($sound);
        $em->flush();

        return new JsonResponse(['id' => $sound->id]);
    }
}