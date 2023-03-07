<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Utils\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\DebugUnitOfWorkListener;
use phpDocumentor\Reflection\Types\This;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;


class WishController extends AbstractController
{

    #[Route('/add_wish', name: 'wish/add')]
    public function add(

        WishRepository $wishRepository,
        Request        $request,
        Censurator $censurator

): Response
    {
        $wish = new Wish();

        $wish
            ->setTitle("Nouveau wish")
            ->setAuthor("Someone")
            ->setDescription("Dernier TP")
            ->setDateCreated(new \DateTime('2023-02-24'))
            ->setIsPublished(true);

        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

         #   /** @var UploadedFile $file */

          #  $file = $wishForm->get('image')->getData();

           # $newFileName = $file->getName() . "-" . uniqid() . "." . $file->guessExtension();
           # $file->move('img/img/wish',$newFileName);
           # $wish->setImage($newFileName);

            $wish->setTitle($censurator->purify($wish->getTitle()));
            $wish->setDescription($censurator->purify($wish->getDescription()));
            
            $wishRepository->save($wish,true);

            $this->addFlash("success", "Vous avez bien ajouté votre wish ! ");

            return $this->redirectToRoute('wish/detail', ['id' => $wish->getId()]);
        }
        return $this->render('wish/add.html.twig', [
            'wishForm' => $wishForm->createView()
        ]);
    }

    #[Route('/wish_list', name: 'wish/list')]
    public function list(WishRepository $wishRepository): Response
    {
        //afficher le titre de toutes les idées publiées
        //   $wishes = $wishRepository ->findAll();
        $wishes = $wishRepository->findBy([], ["dateCreated" => "DESC"]);

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

        if (!$wish) {
            //lance une erreur 404 si la série n'existe pas
            throw $this->createNotFoundException("Oops ! Wish not found !");
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')] public function remove(int $id, WishRepository $wishRepository)
    {
        $wish = $wishRepository->find($id);
        if ($wish) {
            $wishRepository->remove($wish, true);
            $this->addFlash("warning", message: "Suppression du wish !");
        } else {
            throw $this->createNotFoundException("Le wish ne peut pas être supprimé !");
        }
        return $this->redirectToRoute('wish/list');
    }
}