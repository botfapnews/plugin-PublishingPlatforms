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

            //parse parameters from Newscoop image src
            preg_match_all('/([^?&=#]+)=([^&#]*)/', $src, $m);
            $data = array_combine( $m[1], $m[2]);
            $document = $img->ownerDocument;
            $ampImg = $document->createElement('amp-img');
            $ampImg->setAttribute('alt', $alt);
            $ampImg->setAttribute('src', $src);

            try {
                $metaImage = new \MetaImage($data['ImageId']);
                if (isset($data['ImageWidth']) && isset($data['ImageHeight'])) {
                    $width = $data['ImageWidth'];
                    $height = $data['ImageHeight'];
                } else if ($img->getAttribute('width') && $img->getAttribute('height')) {
                    $width = $img->getAttribute('width');
                    $height = $img->getAttribute('height');
                } else {
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

    public static function convertToFBIA($content)
    {
        $document = parent::loadContentToDOMDocument($content);
        $images = $document->getElementsByTagName('img');
        if (count($images) == 0) {
            return $content;
        }

        for ($i = $images->length - 1; $i >= 0; $i --) {
            $img = $images->item($i);
            $document = $img->ownerDocument;
            $figure = $document->createElement('figure');

            $originalImg = clone $img;
            // set orginal image as content of figure tag
            $figure->appendChild($originalImg);

            $img->parentNode->replaceChild($figure, $img);
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
        $figures = $crawler->filter('.cs_img p figure');
        if (count($figures) == 0) {
            return $content;
        }

        foreach ($figures as $figure) {
            $figure->parentNode->parentNode->parentNode->replaceChild($figure, $figure->parentNode->parentNode);
        }

        return $crawler->html();
    }
}
