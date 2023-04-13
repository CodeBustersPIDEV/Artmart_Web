<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/event')]
class EventController extends AbstractController
{

    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $userID = $request->query->get('userID');

        $searchTerm = $request->query->get('q');
        $name = $request->query->get('name');
        $feeOrder = $request->query->get('feeOrder');
        $status = $request->query->get('status');
        $type = $request->query->get('type');

        $users = $entityManager
            ->getRepository(User::class)
            ->findAll();
        
        $events = $eventRepository->findByUser($userID);
       
        if ($name) {
            $events = $eventRepository->findAllSortedByName($name);
        }
        elseif ($feeOrder) {
            $events = $eventRepository->findAllSortedByPrice($feeOrder);
        }
        elseif ($status) {
            $events = $eventRepository->findByStatus($status);
        }
        elseif ($type) {
            $events = $eventRepository->findByType($type);
        }
        else if ($searchTerm) {
            $events = $eventRepository->findByTerm($searchTerm);
        }

        $pages = $paginator->paginate(
            $events, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            9 // Nombre de résultats par page
        );

        return $this->render('event/index.html.twig', [
            'events' => $pages,
            'users' => $users,
            // 'userID' => $userID,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/otherEvents/{id}', name: 'app_event_otherEvents', methods: ['GET'])]
    public function findOtherEvents(EntityManagerInterface $entityManager, $id): Response
    {
        $events = $entityManager
            ->getRepository(Event::class)
            ->findOtherEvents($id);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{eventid}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{eventid}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getEventid(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
