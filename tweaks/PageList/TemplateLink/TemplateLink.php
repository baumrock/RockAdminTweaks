<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class TemplateLink extends Tweak
{
    public function info(): array
    {
        return [
            'description' => "Shows the template name and edit link in the page tree action list for SuperUsers",
        ];
    }


    public function ready(): void
    {
        if (!$this->wire->user->isSuperuser()) {
            return;
        }
        $this->wire->addHookAfter('ProcessPageListActions::getActions', $this, 'addAction');
    }


    public function addAction(HookEvent $event)
    {
        $page = $event->arguments('page');
        $actions = $event->return;
        $template = $page->template;

        $templateEditUrl = $this->config->urls->httpAdmin . 'setup/template/edit?id=' . $template->id;

        $editTemplateAction = [
            'editTemplate' => [
                // use "Edit" to enable built-in long-click feature
                'cn' => 'Edit',
                'name' => $template->name,
                'url' => $templateEditUrl,
            ],
        ];

        // put the template edit action before the Extras (
        $key_extras = array_search('extras', array_keys($actions));

        // home, trash, etc doesn't have 'extras', add the button to the end
        if (!$key_extras) {
            $key_extras = count($actions);
        }

        $actions = array_merge(
            array_slice($actions, 0, $key_extras, true),
            $editTemplateAction,
            array_slice($actions, $key_extras, null, true)
        );

        $event->return = $actions;
    }
}
