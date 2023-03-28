<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Paymentoption;
use App\Entity\Shippingoption;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use mysqli;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        $conn = new mysqli("localhost", "root", "", "artmart");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $stmt = $conn->prepare('SELECT * FROM `order`');
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $order = new Order();
            $user = $this->getDoctrine()->getRepository(User::class)->find($row['UserID']);
            $product = $this->getDoctrine()->getRepository(Product::class)->find($row['ProductID']);
            $shippingOption = $this->getDoctrine()->getRepository(Shippingoption::class)->find($row['ShippingMethod']);
            $paymentOption = $this->getDoctrine()->getRepository(Paymentoption::class)->find($row['PaymentMethod']);
            
            $order->setOrderId($row['order_ID']);
            $order->setUserId($user);
            $order->setProductId($product);
            $order->setQuantity($row['Quantity']);
            $order->setShippingMethod($shippingOption);
            $order->setShippingAddress($row['ShippingAddress']);
            $order->setPaymentMethod($paymentOption);
            $order->setOrderDate($row['OrderDate']);
            $order->setTotalCost($row['TotalCost']);
            $orders[] = $order;
        }

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData(); // create a new Order object from the submitted form data
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{orderId}', name: 'app_order_show', methods: ['GET'])]
    public function show(int $orderId, EntityManagerInterface $entityManager): Response
    {
        $conn = new mysqli("localhost", "root", "", "artmart");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $stmt = $conn->prepare('SELECT * FROM `order` WHERE order_ID = ?');
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $order = null;
        while ($row = $result->fetch_assoc()) {
            $order = new Order();
            $user = $this->getDoctrine()->getRepository(User::class)->find($row['UserID']);
            $product = $this->getDoctrine()->getRepository(Product::class)->find($row['ProductID']);
            $shippingOption = $this->getDoctrine()->getRepository(Shippingoption::class)->find($row['ShippingMethod']);
            $paymentOption = $this->getDoctrine()->getRepository(Paymentoption::class)->find($row['PaymentMethod']);
            
            $order->setOrderId($row['order_ID']);
            $order->setUserId($user);
            $order->setProductId($product);
            $order->setQuantity($row['Quantity']);
            $order->setShippingMethod($shippingOption);
            $order->setShippingAddress($row['ShippingAddress']);
            $order->setPaymentMethod($paymentOption);
            $order->setOrderDate($row['OrderDate']);
            $order->setTotalCost($row['TotalCost']);
        }
    

return $this->render('order/show.html.twig', [
    'order' => $order,
]);
}

    #[Route('/{orderId}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{orderId}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getOrderId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
