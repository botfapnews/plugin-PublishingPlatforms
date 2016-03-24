<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublishingPlatformsController extends Controller
{
    /**
     * @Route("/amp/{languageCode}/{issueUrl}/{sectionUrl}/{articleNumber}/{articleSeo}.htm")
     */
    public function ampAction(Request $request, $languageCode, $issueUrl, $sectionUrl, $articleNumber, $articleSeo = null)
    {
        $em = $this->get('doctrine')->getManager();
        $preferencesService = $this->get('system_preferences_service');
        $templatesService = $this->get('newscoop.templates.service');
        $article = $em->getRepository('Newscoop\Entity\Article')->getArticle($articleNumber, $languageCode)->getOneOrNullResult();
        if (!$article) {
            return new Response($templatesService->fetchTemplate('404.tpl'), 200, array('Content-Type' => 'text/html'));
        }

        $templatesService->setVector(array(
            'publication' => $request->attributes->get('_newscoop_publication_metadata[alias][publication_id]', null, true),
            'language' => $article->getLanguage()->getId(),
            'issue' => $article->getIssue()->getNumber(),
            'section' => $article->getSection()->getNumber(),
            'article' => $article->getNumber(),
        ));

        $smarty = $templatesService->getSmarty();
        $smarty->addTemplateDir(__DIR__.'/../Resources/views/default_templates/');
        $smarty->context()->article = new \MetaArticle($article->getLanguage()->getId(), $article->getNumber());

        // Don't render newscoop javascript reads conunter.
        $defaultCollectStatistics = $preferencesService->CollectStatistics;
        $preferencesService->CollectStatistics = 'N';
        $templateResponse = $templatesService->fetchTemplate('_publishingPlatforms/amp/article.tpl');
        $preferencesService->CollectStatistics = $defaultCollectStatistics;

        return new Response($templateResponse);
    }
}
