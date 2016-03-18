<?php

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
        $article = $em->getRepository('Newscoop\Entity\Article')->getArticle($articleNumber, $languageCode)->getOneOrNullResult();

        $templatesService = $this->get('newscoop.templates.service');
        $templatesService->setVector(array(
            'publication' => $request->attributes->get('_newscoop_publication_metadata[alias][publication_id]', null, true),
            'language' => $article->getLanguage()->getId(),
            'issue' => $article->getIssue()->getNumber(),
            'section' => $article->getSection()->getNumber(),
            'article' => $article->getNumber(),
        ));

        $smarty = $templatesService->getSmarty();
        $smarty->addTemplateDir(__DIR__.'/../Resources/views/default_templates/');
        $smarty->context()->article = new \MetaArticle($language->getId(), $article->getNumber());

        // Don't render newscoop javascript reads conunter.
        $defaultCollectStatistics = $preferencesService->CollectStatistics;
        $preferencesService->CollectStatistics = 'N';
        $templateResponse = $templatesService->fetchTemplate('_publishingPlatforms/amp/article.tpl');
        $preferencesService->CollectStatistics = $defaultCollectStatistics;

        return new Response($templateResponse);
    }
}
