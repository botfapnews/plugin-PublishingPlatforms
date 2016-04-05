<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

abstract class AbstractConverter
{
    /**
     * Get DOMDocument object filled with provided html string
     *
     * @param string $content
     *
     * @return \DOMDocument
     */
    public static function loadContentToDOMDocument($content)
    {
        $document = self::getDOMDocument();
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        return $document;
    }

    /**
     * Get preconfigured DOMDocument object
     *
     * @return \DOMDocument
     */
    public static function getDOMDocument()
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        return $document;
    }
}
