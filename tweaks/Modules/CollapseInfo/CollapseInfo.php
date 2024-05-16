<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;
use ProcessWire\Inputfield;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class CollapseInfo extends Tweak
{
    public function info(): array
    {
        return [
            'description' => 'Collapse Module Info section by default',
        ];
    }


    public function ready(): void
    {
        $this->wire->addHookAfter('InputfieldMarkup::render', function (HookEvent $event) {
            $field = $event->object;
            if ($field->id === 'ModuleInfo') {
                $field->collapsed = Inputfield::collapsedYes;
            }
        });
    }
}
