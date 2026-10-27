<?php

declare(strict_types=1);

namespace Tags\Tests;

use Tags\Model\Tags;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Loop\LoopExecutor;
use Thelia\Model\LangQuery;
use Thelia\Model\Product;
use Thelia\Test\IntegrationTestCase;

abstract class TaggedProductsTestCase extends IntegrationTestCase
{
    protected Product $firstProduct;

    protected Product $secondProduct;

    protected int $langId;

    protected function setUp(): void
    {
        parent::setUp();

        $factory = $this->createFixtureFactory();
        $category = $factory->category();
        $taxRule = $factory->taxRule();
        $currency = $factory->currency();

        $lang = LangQuery::create()->filterByByDefault(1)->findOne() ?? LangQuery::create()->findOne();
        $this->langId = $lang->getId();

        $this->firstProduct = $factory->product($category, $taxRule, $currency);
        $this->firstProduct->setLocale($lang->getLocale())->setTitle('Tagged product 1')->save();

        $this->secondProduct = $factory->product($category, $taxRule, $currency);
        $this->secondProduct->setLocale($lang->getLocale())->setTitle('Tagged product 2')->save();

        $this->tag($this->firstProduct, '__test_tag1');
        $this->tag($this->secondProduct, '__test_tag2');
    }

    protected function tag(Product $product, string $tag): void
    {
        (new Tags())
            ->setSource('product')
            ->setSourceId($product->getId())
            ->setTag($tag)
            ->save();
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return list<array<string, mixed>>
     */
    protected function rows(string $loop, array $arguments): array
    {
        /** @var LoopResult $result */
        $result = $this->getService(LoopExecutor::class)->execute($loop, $arguments);

        $rows = [];
        foreach ($result as $row) {
            $rows[] = $row->getVarVal();
        }

        return $rows;
    }
}
