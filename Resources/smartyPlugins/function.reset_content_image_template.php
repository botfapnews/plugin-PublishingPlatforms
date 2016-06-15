<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Reset template file name used for generating images from article content to default value.
 *
 * Type:     function
 * Name:     reset_content_image_template
 *
 * Example:
 *   {{ reset_content_image_template }}
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_reset_content_image_template($params, &$smarty)
{
    $templatesService = \Zend_Registry::get('container')->get('newscoop.templates.service');
    $templatesService->setContentImageTemplate('editor_image.tpl');

    return;
}
