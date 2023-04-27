<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{

    #[Route('/payment', name: 'app_payment')]
    public function index(Request $request): Response
    {
        $stripeSecretKey = "sk_test_51N1QP2Aj52tRGLXw4eOIn8gSi0X8cSuxFneu5uK8r4GnNqNMOX2sT9Mxm1V8ufJSlSoJlnU4WCdexVHkwZc5Jpih00b9EwhHYI";
        \Stripe\Stripe::setApiKey($stripeSecretKey);
        $YOUR_DOMAIN = 'http://localhost:8000';
        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price' => 'price_1N1RZRAj52tRGLXwrqabXW6N',
                    'quantity' => 1,    
                ]
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);
        return $this->redirect(
            $checkout_session->url
        );
    }

    public function getPrice($ordername):string{
        $stripe = new \Stripe\StripeClient(
            'sk_test_51N1QP2Aj52tRGLXw4eOIn8gSi0X8cSuxFneu5uK8r4GnNqNMOX2sT9Mxm1V8ufJSlSoJlnU4WCdexVHkwZc5Jpih00b9EwhHYI');
          $stripe->prices->create([
            'unit_amount' => 1,
            'currency' => 'ttd',
            'product' => 'prod_Nn1bSC5CKSUQst',
          ]);
          return $this->redirect(
            $checkout_session->url
        );
    }
}