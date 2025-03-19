<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidContact(): void
    {
        $contact = (new Contact())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhoneNumber('0123456789')
            ->setEmail('john.doe@example.com')
            ->setPostalCode('75001')
            ->setCity('Paris');

        $errors = $this->validator->validate($contact);

        $this->assertCount(0, $errors, 'No error messages are expected for a valid contact.');
    }

    public function testInvalidEmail(): void
    {
        $contact = (new Contact())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhoneNumber('0123456789')
            ->setEmail('email-invalide');

        $errors = $this->validator->validate($contact);

        $this->assertGreaterThan(0, count($errors), 'An error message is expected for an invalid email.');
        $this->assertEquals('The email is not valid.', $errors[0]->getMessage());
    }

    public function testBlankFirstName(): void
    {
        $contact = (new Contact())
            ->setFirstName('')
            ->setLastName('Doe')
            ->setPhoneNumber('0123456789')
            ->setEmail('john.doe@example.com');

        $errors = $this->validator->validate($contact);

        $this->assertGreaterThan(0, count($errors), 'An error message is expected for a blank first name.');
        $this->assertEquals('Your first name must be at least 3 characters long.', $errors[0]->getMessage());
    }

    public function testInvalidPhoneNumber(): void
    {
        $contact = (new Contact())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhoneNumber('12345')
            ->setEmail('john.doe@example.com');

        $errors = $this->validator->validate($contact);

        $this->assertGreaterThan(0, count($errors), 'An error message is expected for an invalid phone number.');
        $this->assertEquals('The phone number is not valid.', $errors[0]->getMessage());
    }

    public function testToStringMethod(): void
    {
        $contact = (new Contact())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setPhoneNumber('0123456789')
            ->setEmail('john.doe@example.com')
            ->setCity('Paris')
            ->setPostalCode('75001');

        $expectedString = 'John Doe (0123456789) - john.doe@example.com, Paris [75001]';
        $this->assertEquals($expectedString, (string) $contact, 'The __toString() method should return the expected string.');
    }
}
