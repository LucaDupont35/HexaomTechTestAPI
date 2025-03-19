<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\ContactService;
use Psr\Log\LoggerInterface;
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

    private LoggerInterface $logger;

    public function __construct(ContactService $contactService, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->contactService = $contactService;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    #[Route('', name: 'contact.create', methods: ['POST'])]
    public function create(#[MapRequestPayload(serializationContext:['groups' => ['create']])] Contact $newContact): JsonResponse
    {
        return $this->contactService->save($newContact);
    }

    #[Route('/{id}', name: 'contact.update', methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload(serializationContext:['groups' => ['update']])] Contact $newContact): JsonResponse
    {
        $this->logger->info('Nouveau contact : ' . $newContact->getFirstName());

        $contact = $this->contactService->getById($id);
        $this->logger->info('Contact existant : ' . $contact->getFirstName());

        if (!$contact) {
            return new JsonResponse(['error' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        $newContactData = $this->serializer->normalize($newContact, null, ['groups' => ['update']]);

        $this->serializer->denormalize(
            $newContactData,
            Contact::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $contact]
        );

        $this->logger->info('Contact mis à jour : ' . $contact->getFirstName());

        return $this->contactService->save($contact);
    }

    #[Route('', name: 'contact.list', methods: ['GET'])]
    public function getAllContacts(): JsonResponse
    {
        return $this->json($this->contactService->getAll(), Response::HTTP_OK);
    }
}