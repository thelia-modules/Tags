<?php

declare(strict_types=1);

namespace Tags\Tests;

final class ProductLoopTest extends TaggedProductsTestCase
{
    public function testProductLoopFiltersOnTags(): void
    {
        $rows = $this->product(['tag_match_mode' => 'partial', 'tag' => '__test']);
        self::assertCount(2, $rows);

        $rows = $this->product(['tag_match_mode' => 'partial', 'tag' => 'tag1']);
        self::assertCount(1, $rows);
        self::assertSame($this->firstProduct->getId(), (int) $rows[0]['ID']);

        $rows = $this->product(['tag' => '__test_tag1']);
        self::assertCount(1, $rows);
        self::assertSame($this->firstProduct->getId(), (int) $rows[0]['ID']);

        self::assertCount(2, $this->product(['tag' => '__test_tag1,__test_tag2']));
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return list<array<string, mixed>>
     */
    private function product(array $arguments): array
    {
        return $this->rows('product', $arguments + ['lang' => $this->langId]);
    }
}
