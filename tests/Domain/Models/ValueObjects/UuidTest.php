<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_uuid()
    {
        $value = '1ec3bb95-7875-6e9e-987f-874d31b9d015';
        $uuid  = new Uuid($value);

        $this->assertEquals($uuid->getValue(), $value);
    }

    /**
     * @test
     */
    public function it_test_invalid_uuid()
    {
        $this->expectException(InvalidArgumentException::class);
        new Uuid('invalid-uuid-value');

        $this->expectException(InvalidArgumentException::class);
        new Uuid('');
    }
}
