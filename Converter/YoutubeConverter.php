<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

class YoutubeConverter extends AbstractConverter implements ConverterInterface
{
    /**
     * Get Yutube iframes from html
     *
     * @param \DOMDocument $document
     *
     * @return array
     */
    public static function getElements(\DOMDocument $document)
    {
        $iframes = $document->getElementsByTagName('iframe');
        $youtubeIframes = array();
        for ($i = $iframes->length - 1; $i >= 0; $i --) {
            $iframe = $iframes->item($i);
            $document = $iframe->ownerDocument;
            if (!preg_match('/^(http:|https:)?\/{2}(www.)?(youtube.com)/', $iframe->getAttribute('src'), $matches)) {
                continue;
            }

            $youtubeIframes[] = $iframe;
        }

        return $youtubeIframes;
    }

    /**
     * {@inheritdoc}
     */
    public static function convertToAMP($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $youtubeIframes = self::getElements($document);

        if (count($youtubeIframes) == 0) {
            return $content;
        }

        for ($i = count($youtubeIframes) - 1; $i >= 0; $i --) {
            $iframe = $youtubeIframes[$i];
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $iframe->getAttribute('src'), $match)) {
                $ampYoutube = $document->createElement('amp-youtube');
                $ampYoutube->setAttribute('data-videoid', $match[1]);
                $ampYoutube->setAttribute('layout', 'responsive');

                $ampYoutube->setAttribute('width', 480);
                $ampYoutube->setAttribute('height', 270);
                $iframe->parentNode->replaceChild($ampYoutube, $iframe);
            }
        }

        return $document->saveHtml();
    }

    /**
     * {@inheritdoc}
     */
    public static function convertToFBIA($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $youtubeIframes = self::getElements($document);

        if (count($youtubeIframes) == 0) {
            return $content;
        }

        for ($i = count($youtubeIframes) - 1; $i >= 0; $i --) {
            $youtubeIframe = $youtubeIframes[$i];
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtubeIframe->getAttribute('src'), $match)) {
                $document = $youtubeIframe->ownerDocument;
                $figure = $document->createElement('figure');
                $figure->setAttribute('class', 'op-social');

                $originalYoutubeEmbed = clone $youtubeIframe;
                $figure->appendChild($originalYoutubeEmbed);

                $youtubeIframe->parentNode->replaceChild($figure, $youtubeIframe);
            }
        }

        return $document->saveHtml();
    }
}
