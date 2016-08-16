<?php
/**
 * @package Newscoop\PublishingPlatformsPluginBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2016 Sourcefabric z.u. and contributors.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PublishingPlatformsPluginBundle\Converter;

use Symfony\Component\DomCrawler\Crawler;

class ImagesConverter extends AbstractConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function convertToAMP($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $images = $document->getElementsByTagName('img');
        if (count($images) == 0) {
            return $content;
        }

        for ($i = $images->length - 1; $i >= 0; $i --) {
            $img = $images->item($i);
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt');
            $data = array();

            //parse parameters from Newscoop image src
            preg_match_all('/([^?&=#]+)=([^&#]*)/', $src, $m);
            $document = $img->ownerDocument;
            $ampImg = $document->createElement('amp-img');
            $ampImg->setAttribute('alt', $alt);
            $ampImg->setAttribute('src', $src);

            try {
                if (count($m) > 1 && count($m[1]) > 0 && (count($m[1]) === count($m[2]))) {
                    $data = array_combine($m[1], $m[2]);
                }
                if (isset($data['ImageWidth']) && isset($data['ImageHeight'])) {
                    $width = $data['ImageWidth'];
                    $height = $data['ImageHeight'];
                } else if ($img->getAttribute('width') && $img->getAttribute('height')) {
                    $width = $img->getAttribute('width');
                    $height = $img->getAttribute('height');
                } else {
                    $metaImage = new \MetaImage($data['ImageId']);
                    $size = @getimagesize(APPLICATION_PATH . '/../' . $metaImage->filerpath);
                    $width = $size[0];
                    $height = $size[1];
                }

                $ampImg->setAttribute('width', $width);
                $ampImg->setAttribute('height', $height);
                $ampImg->setAttribute('layout', 'responsive');
            } catch (\Exception $e) {}
            $img->parentNode->replaceChild($ampImg, $img);
        }

        return $document->saveHtml();
    }

    /**
     * {@inheritdoc}
     */
    public static function convertToFBIA($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $images = $document->getElementsByTagName('img');
        if (count($images) == 0) {
            return $content;
        }

        for ($i = $images->length - 1; $i >= 0; $i --) {
            $img = $images->item($i);

            if ($img->getAttribute('data-process') == 'false') {
                // skip image processing - mark it for moving up
                if ($img->parentNode->parentNode->tagName !== 'body') {
                    $img->parentNode->setAttribute('data-converted-from-img', 'true');
                }
                continue;
            }

            $alt = $img->getAttribute('alt');
            $document = $img->ownerDocument;
            $figure = $document->createElement('figure');
            $figure->setAttribute('data-converted-from-img', 'true');

            $originalImg = clone $img;
            // set orginal image as content of figure tag
            $figure->appendChild($originalImg);

            //create figcation if alt is not empty
            if (strlen($alt) > 0) {
                $figcaption = $document->createElement('figcaption', $alt);
                $figure->appendChild($figcaption);
            }

            if ($img->parentNode->tagName == 'figure') {
                $img->parentNode->parentNode->replaceChild($figure, $img->parentNode);
            } else {
                $img->parentNode->replaceChild($figure, $img);
            }
        }

        $content = self::clearCsImage($document->saveHtml());

        return $content;
    }

    /**
     * Replace Newscoop specific image container structure with <figure>
     *
     * @param string $content
     *
     * @return string
     */
    public static function clearCsImage($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $crawler = new Crawler();
        $crawler->addDocument($document);

        $figures = $crawler->filter('figure');
        if (count($figures) > 0) {
            foreach ($figures as $figure) {
                if ($figure->hasAttribute('data-converted-from-img') && $figure->getAttribute('data-converted-from-img') === 'true') {
                    self::moveFigureToTop($figure);
                }
            }
        }

        return $crawler->html();
    }

    public static function moveFigureToTop($node)
    {
        $orginalElement = $node;
        while (
            $node->parentNode != null &&
            $node->parentNode instanceOf \DOMElement &&
            $node->parentNode->tagName != 'html'
        ){

            $node->parentNode->parentNode->replaceChild($orginalElement, $node->parentNode);
            $node = $node->parentNode;
        }
    }
}
