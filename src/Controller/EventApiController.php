<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class EventApiController extends AbstractController
{
    #[Route('/event', name: 'events', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager->getRepository(Event::class)->findAll();

        $responseArray = array();
        foreach ($events as $event) {
            $responseArray[] = array(
                'eventid' => $event->getEventid(),
                'name' => $event->getName(),
                'location' => $event->getLocation(),
                'type' => $event->getType(),
                'description' => $event->getDescription(),
                'entryfee' => $event->getEntryfee(),
                'capacity' => $event->getCapacity(),
                'startdate' => $event->getStartdate()->format('d-m-Y'),
                'enddate' => $event->getEnddate()->format('d-m-Y'),
                'image' => $event->getImage(),
                'status' => $event->getStatus(),
                'user' => $event->getUser()->getUserId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/artist/event/{userID}', name: 'artist_events', methods: ['GET'])]
    public function indexx(EntityManagerInterface $entityManager, $userID): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager
            ->getRepository(Event::class)
            ->createQueryBuilder('e')
            ->andWhere('e.user = :val')
            ->setParameter('val', $userID)
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($events as $event) {
            $responseArray[] = array(
                'eventid' => $event->getEventid(),
                'name' => $event->getName(),
                'location' => $event->getLocation(),
                'type' => $event->getType(),
                'description' => $event->getDescription(),
                'entryfee' => $event->getEntryfee(),
                'capacity' => $event->getCapacity(),
                'startdate' => $event->getStartdate()->format('d-m-Y'),
                'enddate' => $event->getEnddate()->format('d-m-Y'),
                'image' => $event->getImage(),
                'status' => $event->getStatus(),
                'user' => $event->getUser()->getUserId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/event/add', name: 'event_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = new Event();
        
        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);
    
        $event->setName($request->request->get('name'));
        $event->setLocation($request->request->get('location'));
        $event->setType($request->request->get('type'));
        $event->setDescription($request->request->get('description'));
        $event->setEntryfee($request->request->get('entryfee'));
        $event->setCapacity($request->request->get('capacity'));
        $event->setStartdate(new \DateTime($request->request->get('startdate')));
        $event->setEnddate(new \DateTime($request->request->get('enddate')));
        $event->setImage($request->request->get('image'));
        $event->setStatus($request->request->get('status'));
        $event->setUser($user);

        $entityManager->persist($event);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/event/{id}', name: 'event_edit', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($id);

        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);

        if (!$event) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $event->setName($request->request->get('name'));
        $event->setDescription($request->request->get('description'));
        $event->setLocation($request->request->get('location'));
        $event->setType($request->request->get('type'));
        $event->setDescription($request->request->get('description'));
        $event->setEntryfee($request->request->get('entryfee'));
        $event->setCapacity($request->request->get('capacity'));
        $event->setStartdate(new \DateTime($request->request->get('startdate')));
        $event->setEnddate(new \DateTime($request->request->get('enddate')));
        $event->setImage($request->request->get('image'));
        $event->setStatus($request->request->get('status'));
        $event->setUser($user);
       
        $entityManager->persist($event);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/event/{id}', name: 'event_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('The event does not exist');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
