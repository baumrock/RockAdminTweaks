<?php

namespace RockAdminTweaks;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class CheckAllCheckboxes extends Tweak
{
    public function info(): array
    {
        return [
            'description' => 'Add checkbox to check all checkboxes in a field',
        ];
    }

    public function ready(): void
    {
        $this->wire->addHookAfter('InputfieldCheckboxes::renderReady', $this, 'loadJS');
        $this->wire->addHookAfter('InputfieldCheckboxes::renderReady', $this, 'loadCSS');
    }
}
