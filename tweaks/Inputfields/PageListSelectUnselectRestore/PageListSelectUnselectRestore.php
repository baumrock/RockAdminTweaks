<?php

namespace RockAdminTweaks;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class PageListSelectUnselectRestore extends Tweak
{

    public $editedPage = false;

    public function info(): array
    {
        return [
            'description' => 'Add unselect/restore buttons to PageListSelect',
        ];
    }

    public function ready(): void
    {

        $this->loadJS();
        $this->loadCSS();

        $this->addHookAfter('InputfieldPageListSelect::render', $this, 'addPageListUnselectButtons');

    }

    public function addPageListUnselectButtons($event)
    {
        $field = $event->object;

        $originalID = '';
        $originalTitle = ($field->value && $this->pages->get($field->value)) ? $this->pages->get($field->value)->title : '';

        if (isset($this->PageListTweaks) && in_array('pListIDs', $this->PageListTweaks) && $this->wire('user')->isSuperuser()) {
            $originalID = ($field->value && $this->pages->get($field->value)) ? $this->pages->get($field->value)->id : '';
        }

        $restoreTitleTag = strlen($originalTitle) ? 'title="' . \ProcessWire\__('Restore', __FILE__) . ' &quot;' . $originalTitle . '&quot;"' : '';

        $clearButton = '<button class="aos_pagelist_unselect clear ui-button ' . ($field->value ? '' : 'empty') . '" title="' . \ProcessWire\__('Clear', __FILE__) . '"><i class="fa fa-times-circle"></i></button>';

        $restoreButton = $field->value ? '<button class="aos_pagelist_unselect restore ui-button initial" ' . $restoreTitleTag . ' data-title-original="' . $originalTitle . '" data-pid="' . $originalID . '" data-value-original="' . $field->value . '" ><i class="fa fa-undo"></i></button>' : '';

        $event->return = $restoreButton . $clearButton . $event->return;
    }

}
