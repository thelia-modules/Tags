<?php

declare(strict_types=1);

namespace Tags\Tests;

final class TagLoopTest extends TaggedProductsTestCase
{
    public function testPartialMatchFindsEveryTagContainingTheSearch(): void
    {
        self::assertCount(2, $this->rows('tags', ['source' => 'product', 'tag_match_mode' => 'partial', 'tag' => '__test']));

        $rows = $this->rows('tags', ['source' => 'product', 'tag_match_mode' => 'partial', 'tag' => 'tag1']);
        self::assertCount(1, $rows);
        self::assertSame($this->firstProduct->getId(), (int) $rows[0]['SOURCE_ID']);
    }

    public function testExactMatchFindsOnlyTheNamedTag(): void
    {
        $rows = $this->rows('tags', ['source' => 'product', 'tag_match_mode' => 'exact', 'tag' => '__test_tag1']);

        self::assertCount(1, $rows);
        self::assertSame($this->firstProduct->getId(), (int) $rows[0]['SOURCE_ID']);
    }

    public function testExcludeTagLeavesTheOtherTags(): void
    {
        $rows = $this->rows('tags', ['source' => 'product', 'tag_match_mode' => 'partial', 'tag' => '__test', 'exclude_tag' => '__test_tag1']);

        self::assertCount(1, $rows);
        self::assertSame($this->secondProduct->getId(), (int) $rows[0]['SOURCE_ID']);
    }
}
