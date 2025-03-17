<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $contact, bool $flush = true): void
    {
        $this->getEntityManager()->persist($contact);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Contact $contact, bool $flush = true): void
    {
        $this->getEntityManager()->remove($contact);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findById(int $id): ?Contact
    {
        return $this->find($id);
    }

    public function findAllContacts(): array
    {
        return $this->findAll();
    }
}
