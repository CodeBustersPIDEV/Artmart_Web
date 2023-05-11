<?php

namespace App\Controller;

use App\Entity\Eventreport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class EventReportApiController extends AbstractController
{
    #[Route('/report', name: 'reports', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $reports = $entityManager->getRepository(Eventreport::class)->findAll();

        $responseArray = array();
        foreach ($reports as $report) {
            $responseArray[] = array(
                'reportid' => $report->getReportid(),
                'attendance' => $report->getAttendance(),
                'createdAt' => $report->getCreatedAt()->format('d-m-Y'),
                'event' => $report->getEvent()->getEventid()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/report/add', name: 'report_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $report = new Eventreport();

        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);


        $report->setAttendance($request->request->get('attendance'));
        $report->setCreatedAt(new \DateTime($request->request->get('createdAt')));
        $report->setEvent($event);

        $entityManager->persist($report);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/report/{id}', name: 'report_edit', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $report = $entityManager->getRepository(Eventreport::class)->find($id);

        $eventID = $request->request->getInt('event');
        $event = $entityManager->getRepository(Event::class)->find($eventID);

        if (!$report) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $report->setAttendance($request->request->get('attendance'));
        $report->setCreatedAt(new \DateTime($request->request->get('createdAt')));
        $report->setEvent($event);
       
        $entityManager->persist($report);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/report/{id}', name: 'report_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $report = $entityManager->getRepository(report::class)->find($id);

        if (!$report) {
            throw $this->createNotFoundException('The report does not exist');
        }

        $entityManager->remove($report);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
