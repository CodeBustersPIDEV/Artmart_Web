<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Form\ParticipationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;   
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
    private User $connectedUser;

   
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
    #[Route('/admin', name: 'app_participation_index_admin', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $participations = $entityManager
            ->getRepository(Participation::class)
            ->findAll();

        return $this->render('participation/admin/index.html.twig', [
            'participations' => $participations,
        ]);
    }

    #[Route('/admin/new', name: 'app_participation_new_admin', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/admin/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{participationid}', name: 'app_participation_show_admin', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/admin/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/admin/{participationid}/edit', name: 'app_participation_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/admin/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{participationid}', name: 'app_participation_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getParticipationid(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index_admin', [], Response::HTTP_SEE_OTHER);
    }

// ****************************************************************************************
    
    #[Route('/artist', name: 'app_participation_index_artist', methods: ['GET'])]
    public function indexx(EntityManagerInterface $entityManager, EventRepository $eventRepository, Request $request): Response
    {
        $connectedUserID = $this->connectedUser->getUserId();
        $connectedUserRole = $this->connectedUser->getRole();

        $eventID = $request->query->get('eventID');

        $events = $eventRepository->findByUser($connectedUserID);

        $participations = $entityManager
            ->getRepository(Participation::class)
            ->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->where('e.user = :userId')
            ->setParameter('userId', $this->connectedUser->getUserId())
            ->getQuery()
            ->getResult();

        if ($eventID) {
            $participations = $entityManager
                ->getRepository(Participation::class)
                ->createQueryBuilder('e')
                ->andWhere('e.event = :val')
                ->setParameter('val', $eventID)
                ->getQuery()
                ->getResult();
        }
    
        return $this->render('participation/artist/index.html.twig', [
            'participations' => $participations,
            'events' => $events
        ]);
    }

    #[Route('/artist/participate/{eventid}', name: 'app_participation_new_artist', methods: ['GET', 'POST'])]
    public function neww(Request $request, EntityManagerInterface $entityManager, $eventid): Response
    {
        $connectedUserID = $this->connectedUser->getUserId();
    
        
        // Get the event
        $event = $entityManager->getRepository(Event::class)->findOneBy([
            'eventid' => $eventid,
            'status' => ['Scheduled', 'Started'],
        ]);

        // Check if the event is scheduled or started


        // Check if participation already exists
        $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy([
            'user' => $connectedUserID,
            'event' => $eventid,    
        ]);
    
        if ($existingParticipation) {
            // Participation already exists, handle accordingly
            $errorMessage = '<div style="color: red; font-weight: bold;">You are already participating in this event.</div>';
            return new Response($errorMessage, Response::HTTP_CONFLICT);
        }
    
        if (!$event) {
            $errorMessage = '<div style="color: red; font-weight: bold;">You cannot participate in this event at the moment.</div>';
            return new Response($errorMessage, Response::HTTP_CONFLICT);
        }
        
        // Participation does not exist, create new participation
        $participation = new Participation();
        $participation->setUser($entityManager->getReference(User::class, $connectedUserID));
        $participation->setEvent($entityManager->getReference(Event::class, $eventid));
    
        $entityManager->persist($participation);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_event_show_artist', ['eventid' => $participation->getEvent()->getEventid()], Response::HTTP_SEE_OTHER);

    }
    
            
    
    #[Route('/artist/{participationid}', name: 'app_participation_show_artist', methods: ['GET'])]
    public function showw(Participation $participation): Response
    {
        return $this->render('participation/artist/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/artist/{participationid}/edit', name: 'app_participation_edit_artist', methods: ['GET', 'POST'])]
    public function editt(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index_artist', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/artist/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/artist/{participationid}', name: 'app_participation_delete_artist', methods: ['POST'])]
    public function deletee(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($participation->getEvent()->getStatus() == 'Finished' || $participation->getEvent()->getStatus() == 'Started') {
            $errorMessage = '<div style="color: red; font-weight: bold;">You cannot cancel your participation as this event has either started or finished.</div>';
            return new Response($errorMessage, Response::HTTP_CONFLICT);
        } else {
             if ($this->isCsrfTokenValid('delete'.$participation->getParticipationid(), $request->request->get('_token'))) {
                $entityManager->remove($participation);
                $entityManager->flush();
            }           
        }
        return $this->redirectToRoute('app_event_show_artist', ['eventid' => $participation->getEvent()->getEventid()], Response::HTTP_SEE_OTHER);
    }

// ****************************************************************************************
private function AdminAccess()
{
    if ($this->connectedUser->getRole() == "admin") {
        return true; // return a value to indicate that access is allowed
    } else {
        return false; // return a value to indicate that access is not allowed
    }
}
 private function ClientAccess()
{
    if ($this->connectedUser->getRole() === "client") {
        return true; // return a value to indicate that access is allowed
    } else {
        return false; // return a value to indicate that access is not allowed
    }
} 
private function ArtistAccess()
{
    if ($this->connectedUser->getRole() === "artist") {
       return true; // return a value to indicate that access is allowed
    } else {
        return false; // return a value to indicate that access is not allowed
    }
}
private function ArtistClientAccess()
{
    if ($this->connectedUser->getRole() == "artist" || $this->connectedUser->getRole() == "client") {
        return true; // return a value to indicate that access is allowed
    } else {
        return false; // return a value to indicate that access is not allowed
    }
}
}
