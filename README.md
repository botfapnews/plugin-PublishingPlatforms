# Publishing Platforms Plugin

Plugin provides support for Google AMP and Facebook Instant Articles in Newscoop. Plugin creates special route/url for article and enable smarty modifiers which can be used for transforming article body html into AMP/FBIA compatible version.

This plugin is compatible with Newscoop 4.4.7 and higher.

### Usage

***Google AMP***

Modifier:

```{{ $gimme->article->full_text|amp }}```

Route for AMP version of Article:

Pattern: ```/amp/{languageCode}/{issueUrl}/{sectionUrl}/{articleNumber}/{articleSeo}.htm"```

Loaded template path: ```_publishingPlatforms/amp/article.tpl```

Route name: ```newscoop_publishingplatforms_amp_article```

Generate link to AMP version:

```
{{ generate_url route="newscoop_publishingplatforms_amp_article" absolute=true parameters=[
    'languageCode' => $gimme->article->language->code,
    'issueUrl' => $gimme->article->issue->url_name,
    'sectionUrl' => $gimme->article->section->url_name,
    'articleNumber' => $gimme->article->number,
    'articleSeo' => $gimme->article->seo_url_end
] }}

```


***Facebook Instant Articles***

```{{ $gimme->article->full_text|fbia }}```

**Change template file used to rendering of images inside article content**

Before rendering article content field use this function: ```{{ set_content_image_template name="editor_image_fbia.tpl" }}```

Example of ```editor_image_fbia.tpl``` can be found in this plugin: ```Resources/views/default_templates/fbia/editor_image_fbia.tpl```


``` set_content_image_template ``` will tell for Newscoop to use custom template file for images inside article content.

At end of file reset your changes with ```{{ reset_content_image_template }}```



### Commands
#### Install the plugin

``` bash
$ php application/console plugins:install "newscoop/publishingplatforms-plugin-bundle" --env=prod
```

#### Update the plugin

``` bash
$ php application/console plugins:update "newscoop/publishingplatforms-plugin-bundle" --env=prod
```

#### Remove the plugin

``` bash
$ php application/console plugins:remove "newscoop/publishingplatforms-plugin-bundle" --env=prod
```
