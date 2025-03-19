<?php

namespace App\Tests\Repository;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class ContactRepositoryTest extends TestCase
{
    private $entityManager;
    private $managerRegistry;
    private $contactRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->with(Contact::class)
            ->willReturn($this->entityManager);

        $classMetadata = new ClassMetadata(Contact::class);

        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(Contact::class)
            ->willReturn($classMetadata);

        $this->contactRepository = new ContactRepository($this->managerRegistry);
    }

    public function testSave(): void
    {
        $contact = new Contact();
        $contact->setFirstName('John');
        $contact->setLastName('Doe');
        $contact->setEmail('john.doe@example.com');
        $contact->setPhoneNumber('1234567890');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($contact);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->contactRepository->save($contact);
    }

    public function testFindById(): void
    {
        $contactId = 1;
        $contact = new Contact();

        $reflection = new \ReflectionClass($contact);
        $property = $reflection->getProperty('id');
        $property->setValue($contact, $contactId);

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Contact::class, $contactId)
            ->willReturn($contact);

        $result = $this->contactRepository->findById($contactId);

        $this->assertSame($contact, $result);
    }

//    public function testFindAllContacts(): void
//    {
//        $contacts = [
//            (new Contact())->setFirstName('John')->setLastName('Doe'),
//            (new Contact())->setFirstName('Jane')->setLastName('Doe'),
//        ];
//
//        $entityRepository = $this->createMock(ContactRepository::class);
//        $entityRepository->expects($this->once())
//            ->method('findAll')
//            ->willReturn($contacts);
//
//        $this->entityManager->expects($this->once())
//            ->method('getRepository')
//            ->with(Contact::class)
//            ->willReturn($entityRepository);
//
//        $result = $this->contactRepository->findAllContacts();
//
//        $this->assertEquals($contacts, $result);
//    }
}
