<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBookByRef($ref)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchBookByRef($ref)
    {
        return $this->createQueryBuilder('b')
            ->where('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function publishedBooksByAuthorsWithMoreThan10Before2023()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->where('b.published = :published')
            ->andWhere('a.nb_books > :numBooks')
            ->andWhere('b.publicationDate < :beforeDate')
            ->setParameter('published', true)
            ->setParameter('numBooks', 10)
            ->setParameter('beforeDate', new \DateTime('2023-01-01'))
            ->getQuery()
            ->getResult();
    }

    public function updateScienceFictionToRomance()
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction')
            ->getQuery()
            ->execute();
    }
}
