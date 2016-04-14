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

class AMPTransformer extends AbstractTransformer
{
    public function transform($content)
    {
        if (strlen($content) == 0) {
            return $content;
        }

        $content = parent::removeScripts($content);
        $content = parent::removeInlineStyles($content);
        $content = ImagesConverter::convertToAMP($content);
        $content = TwitterConverter::convertToAMP($content);
        $content = FacebookConverter::convertToAMP($content);
        $content = YoutubeConverter::convertToAMP($content);
        $content = InstagramConverter::convertToAMP($content);

        $document = parent::loadContentToDOMDocument($content);
        $mock = parent::getDOMDocument();
        $body = $document->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child){
            $mock->appendChild($mock->importNode($child, true));
        }

        return $mock->saveHtml();
    }
}
