<?php

namespace App\Tests\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Service\ContactService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactServiceTest extends TestCase
{
    private $contactRepository;
    private $validator;
    private $contactService;

    protected function setUp(): void
    {
        $this->contactRepository = $this->createMock(ContactRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->contactService = new ContactService($this->contactRepository, $this->validator);
    }

    public function testSaveValidContact(): void
    {
        $contact = new Contact();
        $contact->setFirstName('John');
        $contact->setLastName('Doe');
        $contact->setEmail('john.doe@example.com');
        $contact->setPhoneNumber('0123456789');

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->contactRepository->expects($this->once())
            ->method('save')
            ->with($contact);

        $response = $this->contactService->save($contact);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['message' => 'Contact saved successfully']),
            $response->getContent()
        );
    }

    public function testSaveInvalidContact(): void
    {
        $contact = new Contact();

        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                'Le prénom ne doit pas être vide.',
                '',
                [],
                '',
                'firstName',
                '',
                null,
                null,
                null
            ),
            new ConstraintViolation(
                'L\'email n\'est pas valide.',
                '',
                [],
                '',
                'email',
                'invalid-email',
                null,
                null,
                null
            ),
        ]);

        $this->validator->method('validate')
            ->willReturn($violations);

        $response = $this->contactService->save($contact);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringContainsString('errors', $response->getContent());
    }

    public function testGetById(): void
    {
        $contact = new Contact();
        $this->contactRepository->method('findById')
            ->willReturn($contact);

        $result = $this->contactService->getById(1);

        $this->assertSame($contact, $result);
    }

    public function testGetAll(): void
    {
        $contacts = [new Contact(), new Contact()];
        $this->contactRepository->method('findAllContacts')
            ->willReturn($contacts);

        $result = $this->contactService->getAll();

        $this->assertSame($contacts, $result);
    }
}
