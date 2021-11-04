<?php

namespace Tests\Invoicing\Domain\Models\ValueObjects;

use Invoicing\Domain\Exceptions\InvalidArgumentException;
use Invoicing\Domain\Models\ValueObjects\Name;
use Invoicing\Domain\Models\ValueObjects\Tax;
use PHPUnit\Framework\TestCase;

class TaxTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_valid_tax(): Tax
    {
        $name  = 'VAT';
        $value = 0.19;

        $tax = new Tax(new Name($name), $value);

        $this->assertEquals($name, $tax->getName()->getValue());
        $this->assertEquals($value, $tax->getValue());

        return $tax;
    }

    /**
     * @test
     * @depends it_test_valid_tax
     */
    public function it_test_valid_tax_with_factory(Tax $tax)
    {
        $taxWithFactory = Tax::create($tax->getName()->getValue(), $tax->getValue());

        $this->assertEquals($tax, $taxWithFactory);
    }

    /**
     * @test
     */
    public function it_test_invalid_negative_value()
    {
        $this->expectException(InvalidArgumentException::class);
        Tax::create('Tax Name', -1);
    }

    /**
     * @test
     */
    public function it_test_invalid_greater_than_one_value()
    {
        $this->expectException(InvalidArgumentException::class);
        Tax::create('Tax Name', 2);
    }

    /**
     * @test
     */
    public function it_test_valid_boundaries_values()
    {
        $tax_0 = new Tax(new Name('VAT'), 0);
        $this->assertEquals(0, $tax_0->getValue());

        $tax_1 = new Tax(new Name('VAT'), 1);
        $this->assertEquals(1, $tax_1->getValue());
    }
}
