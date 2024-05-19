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
            'description' => 'Add Trash action also for non-SuperUsers',
            'author' => 'Roland Toth',
            'authorUrl' => 'https://processwire.com/talk/profile/3156--',
            'maintainer' => 'Adrian Jones',
            'maintainerUrl' => 'https://processwire.com/talk/profile/985--',
            'help' => 'Compared to the core option of ProcessPageList to show trash for non-superusers this tweak only adds the "trash" action to the pagelist actions buttons, but the trash page in the tree will not be accessible with this tweak. If you want to make the trash page also accessible you can use the core feature in ProcessPageList config settings.',
        ];
    }


    public function ready(): void
    {
        if (!$this->wire->user->isSuperuser()) {
            $this->addHookAfter('ProcessPageListActions::getExtraActions', function (HookEvent $event) {
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
