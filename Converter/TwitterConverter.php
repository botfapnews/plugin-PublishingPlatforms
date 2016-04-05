<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

use Symfony\Component\DomCrawler\Crawler;

class TwitterConverter extends AbstractConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function convertToAMP($content)
    {
        $twitterUrlRegex = "/^https?:\/\/twitter\.com\/(?:\#!\/)?(\w+)\/status(es)?\/(\d+)$/";
        $document = parent::loadContentToDOMDocument($content);

        $crawler = new Crawler();
        $crawler->addDocument($document);
        // Search for all twitter embeds
        $embeds = $crawler->filter('blockquote.twitter-tweet');

        if (count($embeds) == 0) {
            return $content;
        }

        foreach ($embeds as $item) {
            $links = $item->getElementsByTagName('a');
            $tweetId = null;
            foreach ( $links as $link ) {
                $href = $link->getAttribute('href');
                if (preg_match($twitterUrlRegex, $href, $matches)) {
                    $tweetId = $matches[3];
                    break;
                }
            }

            $document = $item->ownerDocument;
            $ampTwitter = $document->createElement('amp-twitter');
            $ampTwitter->setAttribute('layout', 'responsive');
            $ampTwitter->setAttribute('width', 486);
            $ampTwitter->setAttribute('height', 657);
            $ampTwitter->setAttribute('data-cards', 'hidden');
            $ampTwitter->setAttribute('data-tweetid', $tweetId);

            // modify orginal blockqoute
            $originalTweet = clone $item;
            $originalTweet->setAttribute('placeholder', false);
            // set orginal tweet as content of amp-twitter tag
            $ampTwitter->appendChild($originalTweet);

            $item->parentNode->replaceChild($ampTwitter, $item);
        }

        return $crawler->html();
    }

    /**
     * {@inheritdoc}
     */
    public static function convertToFBIA($content)
    {
        $twitterUrlRegex = "/^https?:\/\/twitter\.com\/(?:\#!\/)?(\w+)\/status(es)?\/(\d+)$/";
        $document = parent::loadContentToDOMDocument($content);

        $crawler = new Crawler();
        $crawler->addDocument($document);
        // Search for all twitter embeds
        $embeds = $crawler->filter('blockquote.twitter-tweet');

        if (count($embeds) == 0) {
            return $content;
        }

        foreach ($embeds as $item) {
            $document = $item->ownerDocument;
            $figure = $document->createElement('figure');
            $figure->setAttribute('class', 'op-social');

            $originalTwitterEmbed = clone $item;

            $twitterScriptElement = $document->createElement('script');
            $twitterScriptElement->setAttribute('src', 'https:////platform.twitter.com/widgets.js');
            $twitterScriptElement->setAttribute('async', 'true');
            $twitterScriptElement->setAttribute('charset', 'utf-8');

            $iframe = $document->createElement('iframe');
            $iframe->appendChild($originalTwitterEmbed);
            $iframe->appendChild($twitterScriptElement);
            $figure->appendChild($iframe);

            $item->parentNode->replaceChild($figure, $item);
        }

        return $document->saveHtml();
    }
}
