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
            'description' => "Add checkbox to check all checkboxes in a field",
        ];
    }

    public function ready(): void
    {
        $this->loadCSS();
        $this->loadJS();
    }

}
