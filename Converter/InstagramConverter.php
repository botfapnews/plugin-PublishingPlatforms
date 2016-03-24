<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

use Symfony\Component\DomCrawler\Crawler;

class InstagramConverter extends AbstractConverter implements ConverterInterface
{

    public static function convertToAMP($content)
    {
        $instagramUrlRegex = "/^(http:|https:)?\/{2}(www.)?(instagram.com)\/p\/(.*)\//";
        $crawler = new \Symfony\Component\DomCrawler\Crawler();

        $document = parent::loadContentToDOMDocument($content);
        $crawler->addDocument($document);

        // Search for all instagram embeds
        $embeds = $crawler->filter('blockquote.instagram-media');
        foreach ($embeds as $item) {
            $links = $item->getElementsByTagName('a');
            $mediaId = null;
            foreach ( $links as $link ) {
                $href = $link->getAttribute('href');
                if (preg_match($instagramUrlRegex, $href, $matches)) {
                    $mediaId = $matches[4];
                    break;
                }
            }
            $document = $item->ownerDocument;
            $ampInstagram = $document->createElement('amp-instagram');
            $ampInstagram->setAttribute('layout', 'responsive');
            $ampInstagram->setAttribute('width', 400);
            $ampInstagram->setAttribute('height', 400);
            $ampInstagram->setAttribute('data-shortcode', $mediaId);

            $item->parentNode->replaceChild($ampInstagram, $item);
        }

        return $crawler->html();
    }

    public static function convertToFBIA($content)
    {
        $instagramUrlRegex = "/^(http:|https:)?\/{2}(www.)?(instagram.com)\/p\/(.*)\//";
        $crawler = new \Symfony\Component\DomCrawler\Crawler();

        $document = parent::loadContentToDOMDocument($content);
        $crawler->addDocument($document);

        // Search for all instagram embeds
        $embeds = $crawler->filter('blockquote.instagram-media');
        foreach ($embeds as $item) {
            $document = $item->ownerDocument;
            $figure = $document->createElement('figure');
            $figure->setAttribute('class', 'op-social');

            $instagramScriptElement = $document->createElement('script');
            $instagramScriptElement->setAttribute('src', '//platform.instagram.com/en_US/embeds.js');
            $instagramScriptElement->setAttribute('async', 'true');
            $instagramScriptElement->setAttribute('defer', 'true');
            $originalFacebookEmbed = clone $item;

            $iframe = $document->createElement('iframe' );
            $iframe->appendChild($originalFacebookEmbed);
            $iframe->appendChild($instagramScriptElement);
            $figure->appendChild($iframe);

            $item->parentNode->replaceChild($figure, $item);
        }

        return $crawler->html();
    }
}
