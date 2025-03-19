<?php

namespace App\Tests\Controller;

use App\Controller\ContactController;
use App\Entity\Contact;
use App\Service\ContactService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ContactControllerTest extends TestCase
{
    private $contactServiceMock;
    private $serializerMock;
    private $controller;

    protected function setUp(): void
    {
        $this->contactServiceMock = $this->createMock(ContactService::class);
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->controller = new ContactController(
            $this->contactServiceMock,
            $this->serializerMock
        );
    }

    public function testCreate(): void
    {
        $newContact = new Contact();
        $newContact->setFirstName('John');
        $newContact->setLastName('Doe');
        $newContact->setEmail('john.doe@example.com');
        $newContact->setPhoneNumber('0123456789');

        $this->contactServiceMock->expects($this->once())
            ->method('save')
            ->with($newContact)
            ->willReturn(new JsonResponse(['message' => 'Contact saved successfully'], Response::HTTP_OK));

        $response = $this->controller->create($newContact);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Contact saved successfully']),
            $response->getContent()
        );
    }

//    public function testUpdate(): void
//    {
//        $contactId = 1;
//
//        $existingContact = new Contact();
//        $existingContact->setFirstName('Jane');
//        $existingContact->setLastName('Doe');
//        $existingContact->setEmail('jane.doe@example.com');
//        $existingContact->setPhoneNumber('0987654321');
//
//        $updatedContact = new Contact();
//        $updatedContact->setFirstName('Janet');
//        $updatedContact->setLastName('Doe');
//        $updatedContact->setEmail('janet.doe@example.com');
//        $updatedContact->setPhoneNumber('0987654321');
//
//        $this->contactServiceMock->expects($this->once())
//            ->method('getById')
//            ->with($contactId)
//            ->willReturn($existingContact);
//
//        $newContactData = [
//            'firstName' => 'Janet',
//            'lastName' => 'Doe',
//            'email' => 'janet.doe@example.com',
//            'phoneNumber' => '0987654321'
//        ];
//        $this->serializerMock->expects($this->once())
//            ->method('normalize')
//            ->with($updatedContact, null, ['groups' => ['update']])
//            ->willReturn($newContactData);
//
//        $this->serializerMock->expects($this->once())
//            ->method('denormalize')
//            ->with($newContactData, Contact::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $existingContact]);
//
//        $this->contactServiceMock->expects($this->once())
//            ->method('save')
//            ->with($existingContact)
//            ->willReturn(new JsonResponse(['message' => 'Contact saved successfully'], Response::HTTP_OK));
//
//        $response = $this->controller->update($contactId, $updatedContact);
//
//        $this->assertInstanceOf(JsonResponse::class, $response);
//        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
//        $this->assertJsonStringEqualsJsonString(
//            json_encode(['message' => 'Contact saved successfully']),
//            $response->getContent()
//        );
//    }

    public function testUpdateContactNotFound(): void
    {
        $contactId = 1;

        $updatedContact = new Contact();
        $updatedContact->setFirstName('Janet');
        $updatedContact->setLastName('Doe');
        $updatedContact->setEmail('janet.doe@example.com');
        $updatedContact->setPhoneNumber('0987654321');

        $this->contactServiceMock->expects($this->once())
            ->method('getById')
            ->with($contactId)
            ->willReturn(null);

        $response = $this->controller->update($contactId, $updatedContact);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Contact not found']),
            $response->getContent()
        );
    }

//    public function testGetAllContacts(): void
//    {
//        $contacts = [
//            (new Contact())->setFirstName('John')->setLastName('Doe')->setEmail('john.doe@example.com')->setPhoneNumber('0123456789'),
//            (new Contact())->setFirstName('Jane')->setLastName('Doe')->setEmail('jane.doe@example.com')->setPhoneNumber('0987654321'),
//        ];
//
//        $this->contactServiceMock->expects($this->once())
//            ->method('getAll')
//            ->willReturn($contacts);
//
//        $response = $this->controller->getAllContacts();
//
//        $this->assertInstanceOf(JsonResponse::class, $response);
//        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
//        $this->assertJsonStringEqualsJsonString(
//            json_encode($contacts),
//            $response->getContent()
//        );
//    }
}
