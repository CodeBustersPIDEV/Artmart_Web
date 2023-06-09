<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Orderstatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Form\OrderType;
use App\Repository\UserRepository;   
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Chart\ChartType;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


#[Route('/order')]
class OrderController extends AbstractController
{
    private $connectedUser;
    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        if ($session != null) {
            $connectedUserID = $session->get('user_id');
            if (is_int($connectedUserID)) {
                $this->connectedUser = $userRepository->find((int) $connectedUserID);
            }
        }
    }
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
    
    #[Route('export/excel', name: 'app_order_export_el', methods: ['GET','POST'])]
    public function export(EntityManagerInterface $entityManager,Request $request): Response
    { 
        $session = $request->getSession();
        $queryBuilder = $entityManager
        ->getRepository(Order::class)
        ->createQueryBuilder('o')
        ->where('o.userid = :userid')
        ->setParameter('userid', $session->get('user_id'));
        
        $orders = $queryBuilder->getQuery()->getResult();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'Product');
        $sheet->setCellValue('C1', 'Shippingaddress');
        $sheet->setCellValue('E1', 'Order Date');
        $sheet->setCellValue('F1', 'Quantity');
        $sheet->setCellValue('G1', 'Total Price');
        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A'.$row, "000P0".$order->getOrderId());
            $sheet->setCellValue('B'.$row, $order->getProductid()->getName());
            $sheet->setCellValue('C'.$row, $order->getShippingaddress());
            $sheet->setCellValue('E'.$row, $order->getOrderdate());
            $sheet->setCellValue('F'.$row, $order->getQuantity());
            $sheet->setCellValue('G'.$row, $order->getTotalcost());
            $row++;
        }
        $sheet->setCellValue('I1', 'Statistics');
        $sheet->setCellValue('I2', 'Total Cost');
        $sheet->setCellValue('J2', '=SUM(G2:G'.$row.')');
        $sheet->setCellValue('I3', 'Average Quantity');
        $sheet->setCellValue('J3', '=AVERAGE(F2:F'.$row.')');
        $writer = new Xlsx($spreadsheet);
        $filename = 'Orders_Statistics_Artmart_2023.xlsx';
        $writer->save($filename);
    
        $response = new BinaryFileResponse($filename);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
    
        return $response;
    }

    
    #[Route('admin/export/excel', name: 'app_order_export_admin', methods: ['GET','POST'])]
    public function exportAdmin(EntityManagerInterface $entityManager,Request $request): Response
    { 
        $orders = $entityManager
        ->getRepository(Order::class)
        ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'Product');
        $sheet->setCellValue('C1', 'Shippingaddress');
        $sheet->setCellValue('E1', 'Order Date');
        $sheet->setCellValue('F1', 'Quantity');
        $sheet->setCellValue('G1', 'Total Price');
        $sheet->setCellValue('H1', 'User');
        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A'.$row, "000P0".$order->getOrderId());
            $sheet->setCellValue('B'.$row, $order->getProductid()->getName());
            $sheet->setCellValue('C'.$row, $order->getShippingaddress());
            $sheet->setCellValue('E'.$row, $order->getOrderdate());
            $sheet->setCellValue('F'.$row, $order->getQuantity());
            $sheet->setCellValue('G'.$row, $order->getTotalcost());
            $sheet->setCellValue('H'.$row, $order->getUserId()->getName());
            $row++;
        }
        $sheet->setCellValue('I1', 'Statistics');
        $sheet->setCellValue('I2', 'Total Cost');
        $sheet->setCellValue('J2', '=SUM(G2:G'.$row.')');
        $sheet->setCellValue('I3', 'Average Quantity');
        $sheet->setCellValue('J3', '=AVERAGE(F2:F'.$row.')');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Orders_Statistics_Artmart_2023.xlsx';
        $writer->save($filename);
    
        $response = new BinaryFileResponse($filename);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
    
        return $response;
    }

    #[Route('export/Pdf/{id}', name: 'app_order_export_pdf', methods: ['GET','POST'])]
    public function exportOrderPdf($id,EntityManagerInterface $entityManager)
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        $html = '<div style="text-align: center;">
        <h1 style="font-size: 24px; margin-bottom: 20px;">ARTMART</h1>
        <p><strong>Order ID:</strong> '.$order->getOrderId().'</p>
        <p><strong>Product:</strong>'.$order->getProductid()->getName().'</p>
        <p><strong>Shipping Address:</strong> '.$order->getShippingaddress().'</p>
        <p><strong>Order Date:</strong> '.$order->getOrderdate().'</p>
        <p><strong>Quantity:</strong> '.$order->getQuantity().'</p>
        <hr style="margin: 20px 0;">
        <p style="text-align: right;"><strong>Shipping:</strong> '.$order->getShippingmethod()->getShippingfee().' DT</p>
        <p style="text-align: right;"><strong>Total:</strong> $'.$order->getTotalcost().' DT</p>
        <hr style="margin: 20px 0;">
        <p>Thank you for shopping at ArtMart!</p>
        <p>Please contact us if you have any questions or concerns.</p>
        <p>Phone: (216) 29-972-339<br>Email: support@artmmart.com<br>Website: www.artmmart.tn</p>
    </div>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="order-receipt.pdf"'
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

    #[Route('/cancel/{orderId}', name: 'app_order_cancel', methods: ['GET'])]
    public function cancelOrder($orderId,EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        $query = $queryBuilder
        ->select('os')
        ->from(Orderstatus::class, 'os')
        ->where('os.orderid = :orderId')
        ->setParameter('orderId', $orderId)
        ->getQuery();

        $orderStatus = $query->getOneOrNullResult();
        if ($orderStatus == "PENDING") {
            $orderStatus->setStatus('CANCELED');
            $entityManager->flush();
        }

        return $this->redirectToRoute('my_order_index', [], Response::HTTP_SEE_OTHER);
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
