<?php

namespace Tests\Invoicing\Domain\Models;

use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\Recipient;
use Invoicing\Domain\Models\ValueObjects\Address;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_recipient(): Recipient
    {
        $uuid = new Uuid('1ec3cbb4-ca0a-6186-83d5-0242ac140002');
        $name = new Name('Hazem Noor');

        $recipient = new Recipient($uuid, $name);

        $this->assertEquals($uuid, $recipient->getId());
        $this->assertEquals($name, $recipient->getName());
        $this->assertNull($recipient->getAddress());

        return $recipient;
    }

    /**
     * @test
     * @depends it_test_valid_recipient
     */
    public function it_test_valid_recipient_with_factory(Recipient $recipient)
    {
        $recipientWithFactory = Recipient::create($recipient->getId()->getValue(), $recipient->getName()->getValue());

        $this->assertEquals($recipient->getId(), $recipientWithFactory->getId());
        $this->assertEquals($recipient->getName(), $recipientWithFactory->getName());
        $this->assertEquals($recipient->getAddress(), $recipientWithFactory->getAddress());
    }

    /**
     * @test
     * @depends it_test_valid_recipient
     */
    public function it_test_change_name(Recipient $recipient)
    {
        $newName = new Name('Hazem Mohamed Noor');
        $recipient->changeName($newName->getValue());

        $this->assertEquals($newName, $recipient->getName());
    }

    /**
     * @test
     * @depends it_test_valid_recipient
     */
    public function it_test_change_address(Recipient $recipient)
    {
        $currency = Currency::create('EUR', 'Euro');
        $country  = Country::create('DE', 'Germany', $currency);
        $address  = new Address("My Address", $country);

        $recipient->changeAddress($address);
        $this->assertEquals($address, $recipient->getAddress());
    }

    /**
     * @test
     * @depends it_test_valid_recipient
     */
    public function it_test_remove_address(Recipient $recipient)
    {
        $recipient->removeAddress();
        $this->assertNull($recipient->getAddress());
    }
}
