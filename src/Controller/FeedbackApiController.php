<?php

namespace App\Controller;

use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class FeedbackApiController extends AbstractController
{
    #[Route('/feedback', name: 'feedbacks', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $feedbacks = $entityManager->getRepository(Feedback::class)->findAll();

        $responseArray = array();
        foreach ($feedbacks as $feedback) {
            $responseArray[] = array(
                'feedbackid' => $feedback->getFeedbackid(),
                'rating' => $feedback->getRating(),
                'comment' => $feedback->getComment(),
                'date' => $feedback->getDate()->format('d-m-Y'),
                'event' => $feedback->getEvent()->getEventid(),
                'user' => $feedback->getUser()->getUserId()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/feedback/add', name: 'feedback_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $feedback = new Feedback();
        
        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);
    
        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);
    
        $feedback->setRating($request->request->get('rating'));
        $feedback->setComment($request->request->get('comment'));
        $feedback->setDate(new \DateTime($request->request->get('date')));
        $feedback->setEvent($event);
        $feedback->setUser($user);

        $entityManager->persist($feedback);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/feedback/{id}', name: 'feedback_edit', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $feedback = $entityManager->getRepository(Feedback::class)->find($id);

        $userID = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($userID);
    
        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);
    
        if (!$feedback) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $feedback->setRating($request->request->get('rating'));
        $feedback->setComment($request->request->get('comment'));
        $feedback->setDate(new \DateTime($request->request->get('date')));
        $feedback->setEvent($event);
        $feedback->setUser($user);
       
        $entityManager->persist($feedback);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/feedback/{id}', name: 'feedback_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $feedback = $entityManager->getRepository(Feedback::class)->find($id);

        if (!$feedback) {
            throw $this->createNotFoundException('The feedback does not exist');
        }

        $entityManager->remove($feedback);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
