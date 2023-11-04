<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    /**
     * @Route("/author/{name}", name="show_author")
     */
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,
        ]);
    }

    /**
     * @Route("/authors}", name="list_author")
     */
    public function list(): Response
    {
        $authors = [
            [
                'id' => 1,
                'picture' => '/images/Victor-Hugo.jfif',
                'username' => 'Victor Hugo',
                'email' => 'victor.hugo@gmail.com',
                'nb_books' => 100,
            ],
            [
                'id' => 2,
                'picture' => '/images/william-shakespeare.jfif',
                'username' => 'William Shakespeare',
                'email' => 'william.shakespeare@gmail.com',
                'nb_books' => 200,
            ],
            [
                'id' => 3,
                'picture' => '/images/Taha_Hussein.jfif',
                'username' => 'Taha Hussein',
                'email' => 'taha.hussein@gmail.com',
                'nb_books' => 300,
            ],
        ];

        return $this->render('author/list.html.twig', ['authors' => $authors]);
    }


    /**
     * @Route("/authorlist2", name="list_author2")
     */
    public function listAuthors(AuthorRepository $repository): Response
    {
        $authors = $repository->findAll();


        return $this->render('author/showlist.html.twig', ['authors' => $authors]);
    }

    #[Route('/addauthor', name: 'add_author')]
    public function addAuthor(ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $author->setUsername("Arij");
        $author->setEmail("arij@gmail.com");

        #1ere method
        #$em= $this->getDoctrine()->getManager();

        #2methode
        $em = $managerRegistry->getManager();

        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute("list_author2");
    }

    #[Route('/update/{id}', name: 'update_author')]
    public function update($id, AuthorRepository $repository, ManagerRegistry $managerRegistry)
    {
        $author = $repository->find($id);

        $author->setUsername("Ali");
        $author->setEmail("ali@gmail.com");

        $em = $managerRegistry->getManager();

        $em->flush();

        return $this->redirectToRoute("list_author2");
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteAuthor($id, AuthorRepository $repository, ManagerRegistry $managerRegistry)
    {
        $author = $repository->find($id);

        $em = $managerRegistry->getManager();

        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute("list_author2");
    }

    #[Route('/addbyform', name: 'addbyform')]
    public function addByForm(Request $request, ManagerRegistry $managerRegistry)
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute("list_author2");
        }

        //     1ère methode
        /*        return $this->render("author/add.html.twig",
                    array('authorForm'=>$form->createView()));*/


        //        2ème méthode
        return $this->renderForm(
            "author/addbyform.html.twig",
            array('authorForm' => $form)
        );
    }

    #[Route('/author/update/{id}', name: 'author_update')]
    public function updateAuthor($id, Request $request, ManagerRegistry $managerRegistry, AuthorRepository $repository)
    {
        $author = new Author();
        $author = $repository->find($id);

        if (!$author) {
            //Do anything .. display author not found .. 
        }


        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->flush();

            return $this->redirectToRoute("list_author2");
        }

        return $this->render('author/update.html.twig', ['authorForm' => $form->createView()]);
    }

    #[Route('/author/delete/{id}', name: 'author_delete')]
    public function deleteAuthorFromLink($id, ManagerRegistry $managerRegistry, AuthorRepository $repository)
    {
        $author = new Author();
        $author = $repository->find($id);

        if ($author) {
            $em = $managerRegistry->getManager();
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute("list_author2");
    }

    #[Route('/listauthorsbyemail', name: 'list_authors_by_email')]
    public function listAuthorByEmail(AuthorRepository $repository)
    {
        $authors = $repository->findAll();
        $authorsByEmail = $repository->listAuthorByEmail();
        return $this->render(
            "author/authorsbyemail.html.twig",
            array(
                'tabAuthors' => $authors,
                'tabauthorsByEmail' => $authorsByEmail,
            )
        );
    }
}
