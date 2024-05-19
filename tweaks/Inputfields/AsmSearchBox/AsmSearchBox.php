<?php

namespace RockAdminTweaks;

/**
 * Originally from AdminOnSteroids by RolandToth (tpr)
 */
class AsmSearchBox extends Tweak
{

    public $editedPage = false;

    public function info(): array
    {
        return [
            'description' => 'Add search box to ASM Select dropdowns',
        ];
    }

    public function ready(): void
    {

        $this->wire('config')->scripts->add($this->pathToUrl(__DIR__ . '/select2/js/select2.min.js'));
        $this->wire('config')->styles->add($this->pathToUrl(__DIR__ . '/select2/css/select2.min.css'));

        $this->loadJS();
        $this->loadCSS();

        $this->addHookAfter('InputfieldAsmSelect::render', $this, 'addAsmSelectBox');

    }

    public function addAsmSelectBox($event)
    {
        $field = $event->object;

        if ($field->attr('data-no-asm-searchbox') === '1') {
            return;
        }

        $id = $field->attr('id');

        $script = "<script>initAsmSelectBox('$id');</script>";

        $event->return .= $script;
    }

}
