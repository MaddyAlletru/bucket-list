<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\DebugUnitOfWorkListener;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;


class WishController extends AbstractController
{

    #[Route('/add_wish', name: 'wish/add')]
    public function add(

        WishRepository $wishRepository,
        EntityManagerInterface $entityManager): Response
    {
        $wish = new Wish();

        $wish
            ->setTitle("TP Symfony")
            ->setAuthor("Maddy")
            ->setDescription("Réussir le TP")
            ->setDateCreated(new \DateTime('2023-02-23'))
            ->setIsPublished(true);

        $entityManager->persist($wish);
        $entityManager->flush();

        dump($wish);
        return $this->render('wish/add.html.twig', [
        ]);
    }

    #[Route('/wish_list', name: 'wish/list')]
    public function list(WishRepository $wishRepository): Response
    {
    //afficher le titre de toutes les idées publiées
     //   $wishes = $wishRepository ->findAll();
        $wishes = $wishRepository->findBy([],["dateCreated" => "DESC" ]);

        dump($wishes);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);
    }
    #[Route('/wish_detail/{id}', name: 'wish/detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        //afficher le détail d'une idée

        $wish = $wishRepository->find($id);

        if(!$wish){
            //lance une erreur 404 si la série n'existe pas
            throw $this->createNotFoundException("Oops ! Wish not found !");
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
            ]);
    }
}
