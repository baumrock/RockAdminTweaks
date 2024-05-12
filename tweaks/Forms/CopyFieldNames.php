<?php

namespace RockAdminTweaks;

use function ProcessWire\wire;

class CopyFieldNames extends Tweak
{
  public function info(): array
  {
    return [
      'description' => "Copy field names on shift-click by SuperUsers "
        . "<a href=https://processwire.com/talk/topic/29071-using-javascript-to-copy-page-ids-from-page-list-and-field-nameslabels-from-inputfields/ target=_blank><i class='fa fa-info-circle'></i></a>",
    ];
  }

  public function init(): void
  {
    // Add custom JS file to $config->scripts FilenameArray
    // This adds the custom JS fairly early in the FilenameArray which allows for stopping
    // event propagation so clicks on InputfieldHeader do not also expand/collapse InputfieldContent
    if (!$this->wire->user->isSuperuser()) return;
    wire()->addHookBefore(
      'ProcessController::execute',
      function () {
        $this->loadJS();
      }
    );
  }

  public function ready(): void
  {
  }
}
