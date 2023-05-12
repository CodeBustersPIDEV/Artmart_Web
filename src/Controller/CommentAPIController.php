<?php

namespace App\Controller;

use App\Entity\PaymentOption;
use App\Entity\Wishlist;
use App\Entity\Order;
use App\Repository\CommentsRepository;
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
class ApiCommentsController extends AbstractController
{

  private $serializer;

  public function __construct(SerializerInterface $serializer)
  {
    $this->serializer = $serializer;
  }

  #[Route('/AllComments', name: 'app_comments_api', methods: ['GET'])]
  public function index(CommentsRepository $commentRepository): Response
  {
    $comments = $commentRepository->findAll();
    $formatted = $this->serializer->normalize($comments);
    return new JsonResponse($formatted);
  }
}
