<!doctype html>
<html ⚡ lang="{{ $gimme->issue->language->code }}">
  <head>
    <meta charset="utf-8">
    <title>{{ $gimme->article->name }} | {{ $gimme->section->name }} | {{ $gimme->publication->name }}</title>
    <link rel="canonical" href="http://{{ $gimme->publication->site }}{{ uri }}">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "{{ $gimme->article->name }}",
        "datePublished": "{{ $gimme->article->publish_date|camp_date_format:"%Y-%m-%d'T'%H:%M:%S'Z'" }}",
        "image": [
          "{{ $gimme->article->image->getImageUrl() }}"
        ]
      }
    </script>
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>
    <script async custom-element="amp-facebook" src="https://cdn.ampproject.org/v0/amp-facebook-0.1.js"></script>
    <script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>
    <script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>
  </head>
  <body>
    <h1>{{ $gimme->article->name|html_entity_decode|regex_replace:'/&(.*?)quo;/':'&quot;' }}</h1>
    <div id="content">
      {{ $gimme->article->full_text|amp }}
    </div>
  </body>
</html>
