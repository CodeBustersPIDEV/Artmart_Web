<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\WishlistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/wishlist')]
class WishlistController extends AbstractController
{
    #[Route('/', name: 'app_wishlist_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,Request $request): Response
    {
        $session = $request->getSession();
        $queryBuilder = $entityManager
        ->getRepository(Wishlist::class)
        ->createQueryBuilder('w')->where('w.userid LIKE :searchTerm')
        ->setParameter('searchTerm', '%' . $session->get('user_id') . '%');

        $wishlists = $queryBuilder->getQuery()->getResult();

        return $this->render('wishlist/index.html.twig', [
            'wishlists' => $wishlists,
        ]);
    }
    
    #[Route('/delete/{wishlistId}', name: 'app_wishlist_delete_now', methods: ['GET'])]
    public function deleteNow(Request $request, int $wishlistId, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($wishlistId);
        $entityManager->remove($wishlist);
        $entityManager->flush();
        return $this->redirectToRoute('app_wishlist_index');
    }

    #[Route('/new', name: 'app_wishlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = new Wishlist();
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($wishlist);
            $entityManager->flush();

            return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('wishlist/new.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form,
        ]);
    }

    #[Route('/new/{{id}}/{{price}}/{{quantity}}', name: 'app_wishlist_new_client', methods: ['GET', 'POST'])]
    public function newClient(float $price,int $id,string $quantity,Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();

        $wishlist = new Wishlist();

        $wishlist->setUserid($session->get('user_id'));
        $wishlist->setProductid($id);

        $dateObj = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        $wishlist->setDate($dateObj);
        $myFloat = floatval($quantity);
        if (!$myFloat) {
            $myFloat = 1;
        }
        $wishlist->setQuantity(floatval($myFloat));
        $wishlist->setPrice($price);

        $entityManager->persist($wishlist);
        $entityManager->flush();

        return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{wishlistId}', name: 'app_wishlist_show', methods: ['GET'])]
    public function show(Wishlist $wishlist): Response
    {
        return $this->render('wishlist/show.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }

    #[Route('/{wishlistId}/edit', name: 'app_wishlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('wishlist/edit.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form,
        ]);
    }

    #[Route('/{wishlistId}', name: 'app_wishlist_delete', methods: ['POST'])]
    public function delete(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wishlist->getWishlistId(), $request->request->get('_token'))) {
            $entityManager->remove($wishlist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }

}
