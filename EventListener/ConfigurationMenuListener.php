<?php

namespace Newscoop\PublishingPlatformsPluginBundle\EventListener;

use Newscoop\NewscoopBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Translation\Translator;

class ConfigureMenuListener
{
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Newscoop\NewscoopBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $menu[$this->translator->trans('Plugins')]->addChild(
            'PublishingPlatforms Plugin',
            array('uri' => $event->getRouter()->generate('newscoop_publishingplatformsplugin_admin_index'))
        );
    }
}
