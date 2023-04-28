<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use App\Entity\Wishlist;
use Stripe\Price;
use Stripe\Product;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\Paymentoption;
use App\Entity\Product as Pd;
use App\Entity\Orderstatus;


class PaymentController extends AbstractController
{
    //localhost:8000/success
    #[Route('/payment', name: 'app_payment')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $session->get('user_id');
        $queryBuilder = $entityManager
        ->getRepository(Wishlist::class)
        ->createQueryBuilder('w')->where('w.userid LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $session->get('user_id') . '%');
        $wishlists = $queryBuilder->getQuery()->getResult();
        $totalPrice = 0;

        if($wishlists == null){
            return $this->redirect('/');
        }

        foreach ($wishlists as $wishlist) {
            $totalPrice += $wishlist->getPrice()*$wishlist->getQuantity();
        }
        $totalPrice *= 100;
        $stripeSecretKey = "sk_test_51N1QP2Aj52tRGLXw4eOIn8gSi0X8cSuxFneu5uK8r4GnNqNMOX2sT9Mxm1V8ufJSlSoJlnU4WCdexVHkwZc5Jpih00b9EwhHYI";
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $DOMAIN = 'http://localhost:8000';
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => $this->getPrice("Artmart",$totalPrice),
                    'quantity' => 1,    
                ]
            ],
            'mode' => 'payment',
            'success_url' => $DOMAIN . '/success',
            'cancel_url' => $DOMAIN . '/fail',
        ]);
        return $this->redirect(
            $checkout_session->url
        );
    }

    public function getPrice($productName,$price):string{
        Stripe::setApiKey('sk_test_51N1QP2Aj52tRGLXw4eOIn8gSi0X8cSuxFneu5uK8r4GnNqNMOX2sT9Mxm1V8ufJSlSoJlnU4WCdexVHkwZc5Jpih00b9EwhHYI');
        $product = Product::create([
            'name' => $productName,
        ]);
        $price = Price::create([
            'unit_amount' => $price,
            'currency' => 'usd',
            'product' => $product,
        ]);
        return $price->id;

    }

    #[Route('/success', name: 'suc_payment')]
    public function success(Request $request,EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $session->get('user_id');
        $queryBuilder = $entityManager
        ->getRepository(Wishlist::class)
        ->createQueryBuilder('w')->where('w.userid LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $session->get('user_id') . '%');
        $wishlists = $queryBuilder->getQuery()->getResult();
        $totalPrice = 0;

        foreach ($wishlists as $wishlist) {
            $order = new Order();
            $order->setQuantity($wishlist->getQuantity());
            $order->setShippingaddress($session->get('shipping_Address'));
            $order->setTotalcost($wishlist->getPrice());
            $order->setOrderdate(new \DateTime('now', new \DateTimeZone('America/New_York')));
            $order->setUserid($entityManager->getRepository(User::class)->find($session->get('user_id')));
            $order->setProductid($entityManager->getRepository(Pd::class)->find($wishlist->getProductid()));
            $order->setPaymentmethod($entityManager->getRepository(Paymentoption::class)->find(0));
            $entityManager->remove($wishlist);
            $entityManager->persist($order);
            $entityManager->flush();
            
            $orderStat = new Orderstatus();
            $orderStat->setStatus("PENDING");
            $orderStat->setDate(new \DateTime('now', new \DateTimeZone('America/New_York')));
            $orderStat->setOrderid($order);
            $entityManager->persist($orderStat);
            $entityManager->flush();
        }


        header('Location: http://localhost/success.html');
        exit;
    }
    #[Route('/fail', name: 'fail_payment')]
    public function fail(Request $request,EntityManagerInterface $entityManager): Response
    {
        header('Location: http://localhost/fail.html');
        exit;
    }
}