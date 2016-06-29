<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Change template file name used for generating images from article content
 *
 * Type:     function
 * Name:     set_content_image_template
 *
 * Example:
 *   {{ set_content_image_template name="editor_image_fbia.tpl" }}
 *
 * Tip: Don't forget to reset it to default value at end of template file (where this function is used)
 *   {{ reset_content_image_template }}
 *
 * @param array
 *     $params Parameters
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_set_content_image_template($params, &$smarty)
{
    if (!array_key_exists('name', $params)) {
        return;
    }

    $templatesService = \Zend_Registry::get('container')->get('newscoop.templates.service');
    $templatesService->setContentImageTemplate($params['name']);

    return;
}
