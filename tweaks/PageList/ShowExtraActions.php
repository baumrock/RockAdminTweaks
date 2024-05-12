<?php

namespace RockAdminTweaks;

use function ProcessWire\wire;

class ShowExtraActions extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Shows extra page action items in page tree for SuperUsers',
    ];
  }

  public function init(): void
  {
    if (wire()->config->ajax) return;
    wire()->addHookAfter(
      "ProcessPageList::execute",
      function () {
        $this->loadJS();
      }
    );
  }
}
