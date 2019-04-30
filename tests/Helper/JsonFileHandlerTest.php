<?php
namespace App\Tests;

use App\Helper\JsonFileHandler;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class JsonFileHandlerTest extends TestCase
{
    public function testReadingAJsonFile(): void
    {
        $handler = new JsonFileHandler();

        $read = $handler->readJsonFile(__DIR__ . '/test.json');

        self::assertEquals([
            'zero' => null,
            'one' => 1,
            'two' => 2.3,
            'three' => 'text',
            'four' => ['a'],
            'five' => ['x' => 1]
        ], $read);
    }

    /**
     * @testWith [false]
     *           [true]
     */
    public function testWritingAJsonFile(bool $withPrettyPrint): void
    {
        $handler = new JsonFileHandler();

        $filename = tempnam(sys_get_temp_dir(), 'test');

        $toWrite = [
            'zero' => null,
            'one' => 1,
            'two' => 2.3,
            'three' => 'text',
            'four' => ['a'],
            'five' => ['x' => 1]
        ];

        try {
            $handler->writeJsonFile($filename, $toWrite, $withPrettyPrint);

            $written = file_get_contents($filename);

            self::assertEquals(json_encode($toWrite, $withPrettyPrint ? JSON_PRETTY_PRINT : null), $written);
        }
        finally {
            unlink($filename);
        }
    }
}
