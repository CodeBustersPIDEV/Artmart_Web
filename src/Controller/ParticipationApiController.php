<?php

namespace App\Controller;

use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ParticipationApiController extends AbstractController
{
    #[Route('/participation', name: 'participations', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $participations = $entityManager->getRepository(Participation::class)->findAll();

        $responseArray = array();
        foreach ($participations as $participation) {
            $responseArray[] = array(
                'participationid' => $participation->getParticipationid(),
                'registrationdate' => $participation->getRegistrationdate()->format('d-m-Y'),
                'event' => $participation->getEvent()->getEventid(),
                'user' => $participation->getUser()->getUserId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/participation/add', name: 'participation_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $participation = new Participation();

        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);
    
        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);
    
        $participation->setRegistrationdate(new \DateTime($request->request->get('registrationdate')));
        $participation->setEvent($event);
        $participation->setUser($user);

        $entityManager->persist($participation);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/participation/{id}', name: 'participation_edit', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $participation = $entityManager->getRepository(Participation::class)->find($id);

        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);
    
        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);

        if (!$participation) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $participation->setRegistrationdate(new \DateTime($request->request->get('registrationdate')));
        $participation->setEvent($event);
        $participation->setUser($user);
       
        $entityManager->persist($participation);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/participation/{id}', name: 'participation_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $participation = $entityManager->getRepository(Participation::class)->find($id);

        if (!$participation) {
            throw $this->createNotFoundException('The participation does not exist');
        }

        $entityManager->remove($participation);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
