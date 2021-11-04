<?php

namespace Tests\Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Country;
use Invoicing\Domain\Models\ValueObjects\Currency;
use Invoicing\Domain\Repositories\CountryRepository as CountryRepositoryInterface;
use Invoicing\Infrastructure\Repositories\Memory\CountryRepository;
use PHPUnit\Framework\TestCase;

class CountryRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_save_countries(): CountryRepositoryInterface
    {
        $euro    = Currency::create('EUR', 'Euro');
        $germany = Country::create('DE', 'Germany', $euro);

        $egyptianPound = Currency::create('EGP', 'Egyptian Pound');
        $egypt         = Country::create('EG', 'Egypt', $egyptianPound);

        $euro    = Currency::create('EUR', 'Euro');
        $austria = Country::create('AU', 'Austria', $euro);

        $dollar       = Currency::create('USD', 'United States dollar');
        $unitedStates = Country::create('US', 'United States', $dollar);

        /** @var Country[] $countries */
        $countries = [
            $germany,
            $egypt,
            $egypt, // Test duplicate entry, in case of updating entity
            $austria,
            $unitedStates,
        ];

        $countryRepository = new CountryRepository();
        foreach ($countries as $country) {
            $countryRepository->save($country);
        }

        $this->checkStorage($countries, $countryRepository);

        return $countryRepository;
    }

    /**
     * @test
     * @depends it_test_save_countries
     */
    public function it_test_delete_countries(CountryRepositoryInterface $countryRepository)
    {
        $countries = $countryRepository->getAll();

        foreach ($countries as $country) {
            $countryRepository->delete($country);
        }

        $this->checkStorage([], $countryRepository);
    }

    /**
     * @param Country[]         $countries
     * @param CountryRepository $countryRepository
     */
    private function checkStorage(array $countries, CountryRepository $countryRepository)
    {
        $storage = $countryRepository->getAll();

        $ids = array_unique(
            array_map(function (Country $country): string {
                return $country->getId();
            }, $countries)
        );

        $this->assertSameSize($ids, $storage);

        foreach ($countries as $country) {
            $countryFound = $countryRepository->findById($country->getId());

            $this->assertInstanceOf(Country::class, $countryFound);
            if (!is_null($countryFound)) {
                $this->assertEquals($country->getId(), $countryFound->getId());
            }
        }
    }
}
