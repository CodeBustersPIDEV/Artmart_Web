<?php

namespace App\Controller;

use App\Entity\PaymentOption;
use Proxies\__CG__\App\Entity\Shippingoption;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route('/api')]
class OrderApiController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/paymentoption', name: 'app_order_po_api')]
    public function index(): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(PaymentOption::class)->findAll();
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }

    #[Route('/shippingoption', name: 'app_order_so_api')]
    public function indexShip(): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Shippingoption::class)->findAll();
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }

    
    #[Route('/shippingoption/add', name: 'order_api_so_add', methods: ['GET', 'POST'])]
    public function add(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = new Shippingoption();
        
        $so->setName($request->request->get('name'));
        $so->setCarrier($request->request->get('carrier'));
        $so->setShippingspeed($request->request->get('shippingspeed'));
        $so->setShippingfee($request->request->get('shippingfee'));
        $so->setAvailableregions($request->request->get('availableregions'));
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'magicbook.pi@gmail.com';
        $mail->Password = 'wrqfzvitjcovvfqd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('magicbook.pi@gmail.com', 'Departement Gestion Order');
        $mail->addAddress('mahourabensalem@gmail.com', 'You');
        $mail->Subject = 'New Order Shipping Option';
        $mail->Body = 'An admin just added the following shipping option : '.$request->request->get('name').' '.$request->request->get('carrier');
        if ($mail->send()) {
          echo 'cbon ';
        } else {
          echo "echec";
        }
        $entityManager->persist($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    
    #[Route('/shippingoption/{id}', name: 'api_so', methods: ['PUT'])]
    public function edit(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = $entityManager->getRepository(Shippingoption::class)->find($id);

        if (!$so) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $so->setName($request->request->get('name'));
        $so->setCarrier($request->request->get('carrier'));
        $so->setShippingspeed($request->request->get('shippingspeed'));
        $so->setShippingfee($request->request->get('shippingfee'));
        $so->setAvailableregions($request->request->get('availableregions'));

        $entityManager->persist($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
        return $response;
    }

    
    #[Route('/shippingoption/{id}', name: 'api_delete_so', methods: ['DELETE'])]
    public function delet(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = $entityManager->getRepository(Shippingoption::class)->find($id);

        if (!$so) {
            throw $this->createNotFoundException('The Shipping Option does not exist');
        }

        $entityManager->remove($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }
}