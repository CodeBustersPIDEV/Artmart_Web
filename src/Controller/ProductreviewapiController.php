<?php

namespace App\Controller;

use App\Entity\Productreview;
use App\Entity\Readyproduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail as MimeTemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/api')]
class ProductreviewapiController extends AbstractController
{
    #[Route('/productreview/{id}', name: 'app_productreviewapi')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $readyproduct = $entityManager
            ->getRepository(Readyproduct::class)
            ->find($id);
        $productreviews = $entityManager
            ->getRepository(Productreview::class)
            ->findBy(['readyProductId' => $id]);

        $responseArray = [];
        foreach ($productreviews as $productreview) {
            $responseArray[] = [
                'productReviewId' => $productreview->getReviewId(),
                'readyProductId' => $readyproduct->getReadyProductId(),
                'title' => $productreview->getTitle(),
                'text' => $productreview->getText(),
                'rating' => $productreview->getRating(),
                'date' => $productreview->getDate(),
                'user' => $productreview->getUserId()->getUserId(),
            ];
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    #[Route('/productreview/add', name: 'productreview', methods: ['GET', 'POST'])]
    public function addproductreview(Request $request, MailerInterface $mailer): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productreview = new Productreview();
        $productreview->setTitle($request->request->get('title'));
        $productreview->setText($request->request->get('text'));
        $productreview->setRating($request->request->get('rating'));
        $productreview->setDate($request->request->get('date'));
        $idUser = $request->request->getInt('user');
        $user = $entityManager->getRepository(User::class)->find($idUser);
        $productreview->setUserId($user);

        $entityManager->persist($productreview);
        $entityManager->flush();

        $email = (new MimeTemplatedEmail())
            ->from('samar.hamdi@esprit.tn')
            ->to('rima.essaidi@esprit.tn')
            ->subject('A new product review has been added.')
            ->htmlTemplate('emails/new-review.html.twig')
            ->context([
                'username' => $productreview->getReadyProductId()->getUserId(),
                'productreview' => $productreview,
            ]);

        $mailer->send($email);


        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/productreview/{id}', name: 'productreview_delete', methods: ['DELETE'])]
    public function deleteproductreview(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productreview = $entityManager->getRepository(Productreview::class)->find($id);

        if (!$productreview) {
            throw $this->createNotFoundException('The review does not exist');
        }

        $entityManager->remove($productreview);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
}
