<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Paymentoption;
use App\Entity\User;  
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
#region Get
    #[Route('/paymentoption', name: 'app_order_po_api')]
    public function index(): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Paymentoption::class)->findAll();
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }

    #[Route('/shippingoption', name: 'app_o')]
    public function indexShip(): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Shippingoption::class)->findAll();
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }

    #[Route('/list', name: 'list')]
    public function mylist(Request $request): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Wishlist::class)->findBy(['userid' => $request->request->get('id')]);
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }

    
    #[Route('/order', name: 'list_ordees')]
    public function mylistOrder(): Response
    {
        $order = $this->getDoctrine()->getManager()->getRepository(Order::class)->findAll();
        $formatted = $this->serializer->normalize($order);
        return new JsonResponse($formatted);      
    }
#endregion
    /*=======================================================================*/
#region Add 

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
        
        $entityManager->persist($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }

    #[Route('/list/add', name: 'lsit_api', methods: ['GET', 'POST'])]
    public function addList(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = new Wishlist();
        
        $so->setUserid($request->request->get('userid'));
        $so->setProductid($request->request->get('productid'));
        $so->setDate(new \DateTime($request->request->get('date')));
        $so->setQuantity($request->request->get('quantity'));
        $so->setPrice($request->request->get('price'));

        $entityManager->persist($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'added'], Response::HTTP_CREATED);
        return $response;
    }


    #[Route('/order/add', name: 'ordeapi', methods: ['POST'])]
    public function addrder(Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = new Order();
        
        $so->setQuantity($request->request->get('quantity'));
        $so->setShippingaddress($request->request->get('shippingaddress'));
        $so->setOrderdate(new \DateTime());
        $so->setTotalcost($request->request->get('totalcost'));

        $so->setUserid($entityManager->getRepository(User::class)->find($request->request->get('userid')));

        $so->setProductid($entityManager->getRepository(Product::class)->find($request->request->get('productid')));

        $so->setShippingmethod($entityManager->getRepository(ShippingOption::class)->find($request->request->get('shippingmethod')));

        $so->setPaymentmethod($entityManager->getRepository(Proxies\__CG__\App\Entity\Paymentoption::class)->find($request->request->get('paymentmethod')));
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'magicbook.pi@gmail.com';
        $mail->Password = 'wrqfzvitjcovvfqd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('magicbook.pi@gmail.com', 'Department of orders');
        $mail->addAddress('mahourabensalem@gmail.com', 'You');
        $mail->Subject = 'New Order is on hold';
        $mail->Body = 'Your Order is still on hold please continue the payment threw this link :  https://localhost:8000/payment ';
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

#endregion
    /*=======================================================================*/
#region Edit
    #[Route('/list/{id}', name: 'api_list', methods: ['PUT'])]
    public function editList(Request $request, $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = $entityManager->getRepository(Wishlist::class)->find($id);

        if (!$so) {
            return new JsonResponse(['status' => 'Faild']);;
        }

        $so->setUserid($request->request->get('userid'));
        $so->setProductid($request->request->get('productid'));
        $so->setDate(new \DateTime($request->request->get('date')));
        $so->setQuantity($request->request->get('quantity'));
        $so->setPrice($request->request->get('price'));

        $entityManager->persist($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'edited'], Response::HTTP_OK);
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
#endregion
    /*=======================================================================*/
#region Delete
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

    
    #[Route('/shippingoption/{id}', name: 'api_delete_so', methods: ['DELETE'])]
    public function deleteList(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $so = $entityManager->getRepository(Wishlist::class)->find($id);

        if (!$so) {
            throw $this->createNotFoundException('The List does not exist');
        }

        $entityManager->remove($so);
        $entityManager->flush();

        $response = new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
        return $response;
    }


}