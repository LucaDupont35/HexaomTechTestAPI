<?php

namespace App\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContactService
{
    private ContactRepository $contactRepository;
    private ValidatorInterface $validator;

    public function __construct(ContactRepository $contactRepository, ValidatorInterface $validator)
    {
        $this->contactRepository = $contactRepository;
        $this->validator = $validator;
    }

    private function validate(Contact $contact): ?JsonResponse
    {
        $errors = $this->validator->validate($contact);
        if (count($errors) > 0) {
            return new JsonResponse($this->formatErrors($errors), Response::HTTP_BAD_REQUEST);
        }
        return null;
    }

    public function save(Contact $contact): JsonResponse
    {
        if ($errorResponse = $this->validate($contact)) {
            return $errorResponse;
        }

        if (!$contact->getCreatedAt()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
        }
        $contact->setUpdatedAt(new \DateTimeImmutable());

        $this->contactRepository->save($contact);
        return new JsonResponse(['message' => 'Contact saved successfully'], Response::HTTP_OK);
    }

    public function delete(Contact $contact): JsonResponse
    {
        $this->contactRepository->delete($contact);
        return new JsonResponse(['message' => 'Contact deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    public function getById(int $id): ?Contact
    {
        return $this->contactRepository->findById($id);
    }

    public function getAll(): array
    {
        return $this->contactRepository->findAllContacts();
    }

    private function formatErrors($errors): array
    {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()] = $error->getMessage();
        }
        return ['errors' => $formattedErrors];
    }
}