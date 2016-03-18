<?php
/**
 * @package Newscoop/PublishingPlatformsPlugin
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Newscoop/PublishingPlatformsPlugin AMP modifier
 *
 * Type:     modifier
 * Name:     amp
 * Purpose:  Modify content to be valid for AMP syntax
 *
 * @param string $content
 */

function smarty_modifier_amp($content)
{
    $AMPTransformer = new \Newscoop\PublishingPlatformsPluginBundle\Transformer\AMPTransformer();

    return $AMPTransformer->transform($content);
}
