<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Transformer;

use Newscoop\PublishingPlatformsPluginBundle\Converter\ImagesConverter;
use Newscoop\PublishingPlatformsPluginBundle\Converter\TwitterConverter;
use Newscoop\PublishingPlatformsPluginBundle\Converter\FacebookConverter;
use Newscoop\PublishingPlatformsPluginBundle\Converter\YoutubeConverter;
use Newscoop\PublishingPlatformsPluginBundle\Converter\InstagramConverter;

class FBIATransformer extends AbstractTransformer
{
    public function transform($content)
    {
        $content = parent::removeScripts($content);
        $content = parent::removeEmptyTags($content);
        $content = parent::removeInlineStyles($content);
        $content = ImagesConverter::convertToFBIA($content);
        $content = TwitterConverter::convertToFBIA($content);
        $content = FacebookConverter::convertToFBIA($content);
        $content = YoutubeConverter::convertToFBIA($content);
        $content = InstagramConverter::convertToFBIA($content);
        $content = parent::extractFirstClassElementsFromP($content);
        $content = parent::removeEmptyTags($content);

        $document = parent::loadContentToDOMDocument($content);
        $mock = parent::getDOMDocument();
        $body = $document->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child){
            $mock->appendChild($mock->importNode($child, true));
        }

        return $mock->saveHtml();
    }
}
