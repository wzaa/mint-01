<?php
namespace App\Tests\TreeWalker\WalkingStrategy;

use App\CategoryNameExtractor;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CategoryNameExtractorTest extends TestCase
{
    public function testExtractingEmptyList(): void
    {
        $extractor = new CategoryNameExtractor();

        $extracted = $extractor->extractNames([]);

        self::assertEquals([], $extracted);
    }

    public function testExtractingProperList(): void
    {
        $extractor = new CategoryNameExtractor();

        $list = [
            ['category_id' => 123, 'translations' => ['en_US' => ['name' => 'Name 123']]],
            ['category_id' => 321, 'translations' => ['en_US' => ['name' => 'Name 321']]],
        ];

        $extracted = $extractor->extractNames($list, 'en_US');

        self::assertEquals([
            123 => 'Name 123',
            321 => 'Name 321'
        ], $extracted);
    }

    public function testExtractingWhenNoEntriesInThatLanguage(): void
    {
        $extractor = new CategoryNameExtractor();

        $list = [
            ['category_id' => 123, 'translations' => ['pl_PL' => ['name' => 'Name 123']]],
            ['category_id' => 321, 'translations' => ['pl_PL' => ['name' => 'Name 321']]],
        ];

        $extracted = $extractor->extractNames($list, 'ru_RU');

        self::assertEquals([], $extracted);
    }

    public function testExtractingPartiallyInvalidList(): void
    {
        $extractor = new CategoryNameExtractor();

        $list = [
            ['category_id' => 123, 'translations' => ['pl_PL' => ['name' => 'Name 123']]],
            ['category' => 200, 'translations' => ['pl_PL' => ['name' => 'Name 200']]],
            ['category_id' => 300, 'translations2' => ['pl_PL' => ['name' => 'Name 300']]],
            ['category_id' => 310, 'translations' => ['pl_PL' => ['notname' => 'Name 310']]],
            ['category_id' => 321, 'translations' => ['pl_PL' => ['name' => 'Name 321']]],
        ];

        $extracted = $extractor->extractNames($list);

        self::assertEquals([
            123 => 'Name 123',
            321 => 'Name 321'
        ], $extracted);
    }

    public function testExtractingFullyInvalidList(): void
    {
        $extractor = new CategoryNameExtractor();

        $list = [
            [],
            ['key' => 'value'],
            3
        ];

        $extracted = $extractor->extractNames($list);

        self::assertEquals([], $extracted);
    }
}