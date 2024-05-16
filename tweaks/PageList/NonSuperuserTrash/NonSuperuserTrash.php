<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class NonSuperuserTrash extends Tweak
{
    public function info(): array
    {
        return [
            'description' => "Add Trash action also for non-SuperUsers",
        ];
    }


    public function ready(): void
    {
        if (!$this->wire->user->isSuperuser()) {
            $this->addHookAfter('ProcessPageListActions::getExtraActions', function(HookEvent $event) {
                $page = $event->arguments(0);
                $extras = $event->return;
                if ($page->trashable()) {
                    $trash_icon = "<i class='fa fa-trash-o'></i>&nbsp;";
                    $extras['trash'] = array(
                        'cn' => 'Trash aos-pagelist-confirm',
                        'name' => $trash_icon . $this->_('Trash'),
                        'url' => $this->wire('config')->urls->admin . "page/?action=trash&id=$page->id",
                        'ajax' => true,
                    );
                }
                $event->return = $extras;
            });
        }
    }

}
