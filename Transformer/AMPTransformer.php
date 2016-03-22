<?php

namespace Newscoop\PublishingPlatformsPluginBundle\Transformer;

class AMPTransformer
{
    public function transform($content)
    {
        $content = $this->removeInlineStyles($content);
        $content = $this->transformImg($content);
        $content = $this->removeScripts($content);
        $content = $this->transformTwitter($content);
        $content = $this->transformFacebook($content);
        $content = $this->transformYoutube($content);
        $content = $this->transformInstagram($content);

        $document = new \DOMDocument();
        $mock = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $body = $document->getElementsByTagName('body')->item(0);
        foreach ($body->childNodes as $child){
            $mock->appendChild($mock->importNode($child, true));
        }

        return $mock->saveHtml();
    }

    public function removeInlineStyles($content)
    {
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);

        return $content;
    }

    protected function transformImg($content)
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $images = $document->getElementsByTagName('img');

        for ($i = $images->length - 1; $i >= 0; $i --) {
            $img = $images->item($i);
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('width');

            //parse parameters from Newscoop image src
            preg_match_all('/([^?&=#]+)=([^&#]*)/', $src, $m);
            $data = array_combine( $m[1], $m[2]);

            $ampImg = $document->createElement('amp-img');
            $ampImg->setAttribute('alt', $alt);
            $ampImg->setAttribute('src', $src);

            try {
                $metaImage = new \MetaImage($data['ImageId']);
                if (isset($data['ImageWidth']) && isset($data['ImageHeight'])) {
                    $width = $data['ImageWidth'];
                    $height = $data['ImageHeight'];
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

    protected function removeScripts($content)
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $scripts = $document->getElementsByTagName('script');
        for ($i = $scripts->length - 1; $i >= 0; $i --) {
            $item = $scripts->item($i);
            $item->parentNode->removeChild($item);
        }

        return $document->saveHTML();
    }

    protected function transformTwitter($content)
    {
        $twitterUrlRegex = "/^https?:\/\/twitter\.com\/(?:\#!\/)?(\w+)\/status(es)?\/(\d+)$/";
        $crawler = new \Symfony\Component\DomCrawler\Crawler();

        // fill Crawler with already created document to be able to use LIBXML_* parameters
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $crawler->addDocument($document);

        // Search for all twitter embeds
        $embeds = $crawler->filter('blockquote.twitter-tweet');
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

    public function transformFacebook($content)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler();

        // fill Crawler with already created document to be able to use LIBXML_* parameters
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $crawler->addDocument($document);

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

    public function transformYoutube($content)
    {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $iframes = $document->getElementsByTagName('iframe');

        for ($i = $iframes->length - 1; $i >= 0; $i --) {
            $iframe = $iframes->item($i);
            if (!preg_match('/^(http:|https:)?\/{2}(www.)?(youtube.com)/', $iframe->getAttribute('src'), $matches)) {
                continue;
            }

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

    public function transformInstagram($content)
    {
        $instagramUrlRegex = "/^(http:|https:)?\/{2}(www.)?(instagram.com)\/p\/(.*)\//";
        $crawler = new \Symfony\Component\DomCrawler\Crawler();

        // fill Crawler with already created document to be able to use LIBXML_* parameters
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
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
}
