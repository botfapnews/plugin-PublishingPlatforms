<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Transformer;

use Newscoop\PublishingPlatformsPluginBundle\Converter\AbstractConverter;

abstract class AbstractTransformer extends AbstractConverter
{
    public static function removeInlineStyles($content)
    {
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);

        return $content;
    }

    public static function removeScripts($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $scripts = $document->getElementsByTagName('script');
        for ($i = $scripts->length - 1; $i >= 0; $i --) {
            $item = $scripts->item($i);
            $item->parentNode->removeChild($item);
        }

        return $document->saveHTML();
    }

    public static function removeEmptyTags($content)
    {
        $content = preg_replace('/ {2,}/', ' ', str_replace('&nbsp;', ' ', $content));
        $content = preg_replace ('/<([^<\/>]*)>([\s]*?|(?R))<\/\1>/imsU', '', $content);

        return $content;
    }

    public function extractFirstClassElementsFromP($content)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler();
        $crawler->addDocument(parent::loadContentToDOMDocument($content));

        // find all first class elements inside p
        $elements = $crawler->filter('p figure');
        foreach ($elements as $item) {
            if(isset($item->parentNode)) {
                $item->parentNode->parentNode->appendChild($item);
            }
        }

        return $crawler->html();
    }
}
