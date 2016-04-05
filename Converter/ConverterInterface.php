<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

interface ConverterInterface
{
    /**
     * Find elements in provided string and covert them to APM versions.
     *
     * @param string $content String to be converted
     *
     * @return string Converted string
     */
    public static function convertToAMP($content);

    /**
     * Find elements in provided string and covert them to FBIA versions.
     *
     * @param string $content String to be converted
     *
     * @return string Converted string
     */
    public static function convertToFBIA($content);
}
