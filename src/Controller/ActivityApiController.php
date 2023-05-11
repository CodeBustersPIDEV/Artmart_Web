<?php

namespace App\Controller;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ActivityApiController extends AbstractController
{
    #[Route('/activity', name: 'activities', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $activities = $entityManager->getRepository(Activity::class)->findAll();

        $responseArray = array();
        foreach ($activities as $activity) {
            $responseArray[] = array(
                'activityid' => $activity->getActivityid(),
                'title' => $activity->getTitle(),
                'host' => $activity->getHost(),
                'date' => $activity->getDate()->format('d-m-Y'),
                'event' => $activity->getEvent()->getEventid(),
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/activity/add', name: 'activity_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $activity = new Activity();

        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);
    
        $activity->setTitle($request->request->get('title'));
        $activity->setHost($request->request->get('host'));
        $activity->setDate(new \DateTime($request->request->get('date')));
        $activity->setEvent($event);

        $entityManager->persist($activity);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/activity/{id}', name: 'activity_edit', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $activity = $entityManager->getRepository(Activity::class)->find($id);

        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);
    
        if (!$activity) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $activity->setTitle($request->request->get('title'));
        $activity->setHost($request->request->get('host'));
        $activity->setDate(new \DateTime($request->request->get('date')));
        $activity->setEvent($event);
       
        $entityManager->persist($activity);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/activity/{id}', name: 'activity_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $activity = $entityManager->getRepository(Activity::class)->find($id);

        if (!$activity) {
            throw $this->createNotFoundException('The activity does not exist');
        }

        $entityManager->remove($activity);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
