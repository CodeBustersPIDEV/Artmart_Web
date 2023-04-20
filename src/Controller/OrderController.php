<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Orderstatus;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;


#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $orders = $entityManager
            ->getRepository(Order::class)
            ->findAll();
    
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
    #[Route('/myOrders', name: 'my_order_index', methods: ['GET'])]
    public function myIndexOrder(EntityManagerInterface $entityManager,Request $request): Response
    {
        $session = $request->getSession();
        $queryBuilder = $entityManager
        ->getRepository(Order::class)
        ->createQueryBuilder('o')
        ->where('o.userid = :userid')
        ->setParameter('userid', $session->get('user_id'));
        
        $orders = $queryBuilder->getQuery()->getResult();
        $orderStatuses = [];
        foreach ($orders as $order) {
            $orderStatus = $entityManager->getRepository(Orderstatus::class)->findOneBy(['orderid' => $order->getOrderId()]);
        
            $status = $orderStatus ? $orderStatus->getStatus() : null;
            // Store $orderStatus somewhere for display later with the order details
            $order->status = $status;
        }

        return $this->render('order/myorders.html.twig', [
            'orders' => $orders        
        ]);
    }
    
    #[Route('/admin', name: 'app_order_admin', methods: ['GET'])]
    public function showAdmin(EntityManagerInterface $entityManager): Response
    {        
        $orders = $entityManager
        ->getRepository(Order::class)
        ->findAll();
        return $this->render('order/admin.html.twig', [
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
            $totalcost = $form->get('totalcost')->getData();
            if (!is_numeric($totalcost) && $totalcost !== null) {
                $form->get('totalcost')->addError(new FormError('Total cost must be a numeric string.'));
                return $this->renderForm('order/new.html.twig', [
                    'order' => $order,
                    'form' => $form,
                ]);
            }
    
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
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{orderId}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $orderId, EntityManagerInterface $entityManager): Response
    {
        $order = $entityManager->getRepository(Order::class)->find($orderId);
        $order->setupOrderDate('2023-04-06 00:00:00');
        
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
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

    #[Route('/order/gui', name: 'app_order_gui', methods: ['GET'])]
    public function indexGui(EntityManagerInterface $entityManager): Response
    {
        return $this->render('order/orderGui.html.twig', [
        ]);
    }

}
