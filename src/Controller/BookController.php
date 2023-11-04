<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("/booklist3", name="list_books")
     */
    public function listBooks(BookRepository $repository)
    {
        $books = $repository->findAll();

        return $this->render(
            "book/listbooks.html.twig",
            array('tabBooks' => $books)
        );
    }

    #[Route('/addbookbyform', name: 'addbookbyform')]
    public function addbookbyform(Request $request, ManagerRegistry $managerRegistry)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();

            $book->setPublished('true');
            $nbrBook = $book->getAuthor()->getNbBooks();

            $book->getAuthor()->setNbBooks($nbrBook + 1);

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute("list_books");
        }

        return $this->renderForm(
            "book/addbookbyform.html.twig",
            array('bookForm' => $form)
        );
    }

    #[Route('/updateBook/{ref}', name: 'book_update')]
    public function updateBook($ref, BookRepository $repository, ManagerRegistry $manager, Request $request): Response
    {
        $book = $repository->find($ref);
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $manager->getManager();
            $em->flush();
            return $this->redirectToRoute('list_books');
        }
        return $this->renderForm('book/updateBook.html.twig', ['form' => $form]);
    }

    #[Route('/delete/{ref}', name: 'delete_book')]
    public function deleteBook($ref, BookRepository $repository, ManagerRegistry $managerRegistry)
    {
        $book = $repository->findBookByRef($ref);

        if ($book) {
            $em = $managerRegistry->getManager();
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute("list_books");
    }

    /**
     * @Route("/showBook/{ref}", name="show_book")
     */
    public function showBook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);

        if (!$book) {
            //Book not found
            //to tryyyyyy: throwing an exception ... 
            throw $this->createNotFoundException('The book does not exist.');
        }

        return $this->render('book/showdetails.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/booksearchbyref", name="search_book_by_ref")
     */
    public function searchBookByRef(BookRepository $repository,  Request $request)
    {
        $ref = $request->query->get('ref');
        $books = $repository->searchBookByRef($ref);

        return $this->render(
            "book/listbooksbyref.html.twig",
            array('tabBooks' => $books)
        );
    }
}
