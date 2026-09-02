<?php

declare(strict_types=1);

/*************************************************************************************/
/*      Copyright (c) OpenStudio                                                     */
/*      web : https://www.openstudio.fr                                              */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Tags\Twig;

use Propel\Runtime\ActiveQuery\Criteria;
use Tags\Model\TagsQuery;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig counterpart of the Smarty has_tag plugin (see Tags\Smarty\TagsPlugin).
 */
final class TagsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_tag', [$this, 'hasTag']),
        ];
    }

    /**
     * @param string|array<int|string> $tag one tag, several tags separated by commas, or an array of tags
     */
    public function hasTag(int $id, string $source, string|array $tag): bool
    {
        $tagList = \is_array($tag) ? $tag : explode(',', $tag);

        $tagList = array_filter(
            array_map(static fn ($value): string => trim((string) $value), $tagList),
            static fn (string $value): bool => '' !== $value
        );

        if ([] === $tagList) {
            return false;
        }

        return TagsQuery::create()
            ->filterBySourceId($id)
            ->filterBySource($source)
            ->filterByTag($tagList, Criteria::IN)
            ->count() > 0;
    }
}
