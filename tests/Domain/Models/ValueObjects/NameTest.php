<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Name;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_name()
    {
        $myName = 'Hazem';
        $name   = new Name($myName);

        $this->assertEquals($myName, $name->getValue());
    }

    /**
     * @test
     */
    public function it_test_empty_value()
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('');
    }
}
