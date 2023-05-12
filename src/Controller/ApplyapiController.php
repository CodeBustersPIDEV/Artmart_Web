<?php

namespace App\Controller;
use App\Entity\Apply;
use App\Form\ApplyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\JsonResponse;  
#[Route('/api')]
class ApplyapiController extends AbstractController
{
    #[Route('/apply', name: 'app_applyapi', methods:['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $apply = $entityManager
        ->getRepository(Apply::class)
        ->findAll();
        $responseArray = array();
        foreach ($apply as $apply) {
            $responseArray[] = array(
                'applyId' => $apply->getApplyId(),
                'status' => $apply->getStatus(),
                'artist' => $apply->getArtist()->getName(),
                'customproduct' => $apply->getCustomproduct()->getProduct()->getName()
            );
        }

        $responseData = json_encode($responseArray);
        $response = new Response($responseData);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route('/apply/{applyId}/apply', name: 'app_apply_apply', methods: ['GET', 'POST'])]
    public function finish(int $applyId): JsonResponse

    {
        $sid    = "AC85fdc289caf6aa747109220798d39394";
        $token  = "a9cb650c5fb9a222038460d4311dc240";
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+21698238240", 
            array(
              "from" => "whatsapp:+14155238886",
              "body" => "your apply is done !"
            )
            );
        $entityManager = $this->getDoctrine()->getManager();

        $apply = $entityManager->getRepository(Apply::class)->find($applyId);

        if (!$apply) {
            throw $this->createNotFoundException('Unable to find apply entity.');
        }

       
        $apply->setStatus('done');

        $entityManager->persist($apply);
        $entityManager->flush();
        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    #[Route('/apply/{id}', name: 'apply_delete', methods: ['DELETE'])]
    public function deleteapply(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $apply = $entityManager->getRepository(Apply::class)->find($id);

        if (!$apply) {
            throw $this->createNotFoundException('The apply does not exist');
        }

        $entityManager->remove($apply);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }

}
