<?php

namespace Tests\Invoicing\Infrastructure\Repositories\Memory;

use Invoicing\Domain\Models\Recipient;
use Invoicing\Domain\Repositories\RecipientRepository as RecipientRepositoryInterface;
use Invoicing\Infrastructure\Repositories\Memory\RecipientRepository;
use PHPUnit\Framework\TestCase;

class RecipientRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_test_save_recipients(): RecipientRepositoryInterface
    {
        $ids = [
            "1ec3d3c0-0ace-67fe-bdd7-0242ac140002",
            "1ec3d3c0-0ad2-6674-817a-0242ac140002",
            "1ec3d3c0-0ad2-6e08-8dd2-0242ac140002",
            "1ec3d3c0-0ad3-6506-be28-0242ac140002",
            "1ec3d3c0-0ad3-6bb4-9178-0242ac140002",
            "1ec3d3c0-0ad4-6244-9420-0242ac140002",
            "1ec3d3c0-0ad4-68b6-9b4b-0242ac140002",
            "1ec3d3c0-0ad4-6f14-b660-0242ac140002",
            "1ec3d3c0-0ad5-6568-b850-0242ac140002",
            "1ec3d3c0-0ad5-6c7a-8457-0242ac140002",
        ];

        /** @var Recipient[] $recipients */
        $recipients = [];
        foreach ($ids as $i => $id) {
            $recipients[] = Recipient::create($id, sprintf("Recipient no. %s", $i + 1));
        }

        $recipientRepository = new RecipientRepository();
        foreach ($recipients as $recipient) {
            $recipientRepository->save($recipient);
        }

        $this->checkStorage($recipients, $recipientRepository);

        return $recipientRepository;
    }

    /**
     * @test
     * @depends it_test_save_recipients
     */
    public function it_test_delete_recipients(RecipientRepositoryInterface $recipientRepository)
    {
        $recipients = $recipientRepository->getAll();

        foreach ($recipients as $recipient) {
            $recipientRepository->delete($recipient);
        }

        $this->checkStorage([], $recipientRepository);
    }

    /**
     * @param Recipient[]         $recipients
     * @param RecipientRepository $recipientRepository
     */
    private function checkStorage(array $recipients, RecipientRepository $recipientRepository)
    {
        $storage = $recipientRepository->getAll();

        $ids = array_unique(
            array_map(function (Recipient $recipient): string {
                return $recipient->getId()->getValue();
            }, $recipients)
        );

        $this->assertSameSize($ids, $storage);

        foreach ($recipients as $recipient) {
            $recipientFound = $recipientRepository->findById($recipient->getId()->getValue());

            $this->assertInstanceOf(Recipient::class, $recipientFound);
            if (!is_null($recipientFound)) {
                $this->assertEquals($recipient->getId(), $recipientFound->getId());
            }
        }
    }
}
