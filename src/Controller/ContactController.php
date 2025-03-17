<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api/contact')]
class ContactController extends AbstractController
{
    private ContactService $contactService;
    private SerializerInterface $serializer;

    public function __construct(ContactService $contactService, SerializerInterface $serializer)
    {
        $this->contactService = $contactService;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'contact.create', methods: ['POST'])]
    public function create(#[MapRequestPayload(serializationContext:['groups' => ['create']])] Contact $newContact): JsonResponse
    {
        return $this->contactService->save($newContact);
    }

    #[Route('/{id}', name: 'contact.update', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload(serializationContext:['groups' => ['update']])] Contact $newContact): JsonResponse
    {
        $contact = $this->contactService->getById($id);
        if (!$contact) {
            return new JsonResponse(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        $this->serializer->deserialize(
            json_encode($newContact),
            Contact::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $contact, 'groups' => ['contact.update']]
        );

        return $this->contactService->save($contact);
    }

    #[Route('/{id}', name: 'contact.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $contact = $this->contactService->getById($id);
        if (!$contact) {
            return new JsonResponse(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->contactService->delete($contact);
    }

    #[Route('/{id}', name: 'contact.show', methods: ['GET'])]
    public function getContact(int $id): JsonResponse
    {
        $contact = $this->contactService->getById($id);
        if (!$contact) {
            return new JsonResponse(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($contact, Response::HTTP_OK);
    }

    #[Route('', name: 'contact.list', methods: ['GET'])]
    public function getAllContacts(): JsonResponse
    {
        return $this->json($this->contactService->getAll(), Response::HTTP_OK);
    }
}