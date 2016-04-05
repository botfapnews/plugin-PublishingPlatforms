<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

use Symfony\Component\DomCrawler\Crawler;

class FacebookConverter extends AbstractConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function convertToAMP($content)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler();
        $crawler->addDocument(parent::loadContentToDOMDocument($content));

        // remove add fb-root
        $crawler->filter('#fb-root')->each(function ($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        // Search for all facebook posts
        $posts = $crawler->filter('div.fb-post');
        foreach ($posts as $item) {
            $document = $item->ownerDocument;
            $ampFacebook = $document->createElement('amp-facebook');
            $ampFacebook->setAttribute('layout', 'responsive');
            $ampFacebook->setAttribute('width', $item->hasAttribute('data-width') ? $item->getAttribute('data-width') : 486);
            $ampFacebook->setAttribute('height', 657);
            $ampFacebook->setAttribute('data-href', $item->getAttribute('data-href'));

            $item->parentNode->replaceChild($ampFacebook, $item);
        }

        // Search for all facebook videos
        $videos = $crawler->filter('div.fb-video');
        foreach ($videos as $item) {
            $document = $item->ownerDocument;
            $ampFacebook = $document->createElement('amp-facebook');
            $ampFacebook->setAttribute('layout', 'responsive');
            $ampFacebook->setAttribute('width', 552);
            $ampFacebook->setAttribute('height', 322);
            $ampFacebook->setAttribute('data-href', $item->getAttribute('data-href'));
            $ampFacebook->setAttribute('data-embed-as', 'video');

            $item->parentNode->replaceChild($ampFacebook, $item);
        }

        return $crawler->html();
    }

    /**
     * {@inheritdoc}
     */
    public static function convertToFBIA($content)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler();
        $crawler->addDocument(parent::loadContentToDOMDocument($content));

        // remove add fb-root
        $crawler->filter('#fb-root')->each(function ($crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $embeds = $crawler->filter('div.fb-post, div.fb-video');
        if (count($embeds) == 0) {
            return $content;
        }

        $fbJsScriptCode = "(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3\"; fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));";
        foreach ($embeds as $item) {
            $document = $item->ownerDocument;
            $figure = $document->createElement('figure');
            $figure->setAttribute('class', 'op-social');

            $fbRoot = $document->createElement('div');
            $fbRoot->setAttribute('id', 'fb-root');

            $fbScriptElement = $document->createElement('script', $fbJsScriptCode);
            $originalFacebookEmbed = clone $item;

            $iframe = $document->createElement('iframe');
            $iframe->appendChild($fbRoot);
            $iframe->appendChild($fbScriptElement);
            $iframe->appendChild($originalFacebookEmbed);
            $figure->appendChild($iframe);

            $item->parentNode->replaceChild($figure, $item);
        }

        return $crawler->html();
    }
}
