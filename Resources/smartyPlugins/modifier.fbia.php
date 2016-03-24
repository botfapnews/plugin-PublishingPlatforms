<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Newscoop/PublishingPlatformsPlugin Facebook Instant Articles modifier
 *
 * Type:     modifier
 * Name:     amp
 * Purpose:  Modify content to be valid for FBIA syntax
 *
 * @param string $content
 */

function smarty_modifier_fbia($content)
{
    $FBIATransformer = new \Newscoop\PublishingPlatformsPluginBundle\Transformer\FBIATransformer();

    return $FBIATransformer->transform($content);
}
