<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

use function ProcessWire\wire;
use function ProcessWire\wireIconMarkup;

class ImageDownload extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Adds a download icon to image fields.',
      'help' => 'Optional longer description - opens in a **modal** and supports [markdown](/).',
      'author' => 'Bernhard Baumrock',
      'authorUrl' => 'https://processwire.com/talk/profile/2137-bernhard/',
      'infoLink' => 'https://processwire.com/talk/topic/28089--',
    ];
  }

  public function init(): void
  {
    wire()->addHookAfter(
      'InputfieldImage::getImageThumbnailActions',
      function (HookEvent $event) {
        $image = $event->arguments(0); // Pageimage
        $class = $event->arguments(3); // class to use on all returned actions
        $a = $event->return; // array
        $icon = wireIconMarkup('download');
        $a['download'] = "<a class='$class gridImage__trash' href='$image->url' download>$icon</a>";
        $event->return = $a;
      }
    );
    wire()->addHookAfter(
      'InputfieldImage::getImageEditButtons',
      function (HookEvent $event) {
        $image = $event->arguments(0); // Pageimage
        $class = $event->arguments(3); // class(es) to use on all returned actions
        $buttons = $event->return; // array, indexed by action name
        $icon = wireIconMarkup('download');
        $buttons['download'] = "<button class='$class gridImage__trash' type='button'><a download href='$image->url'>$icon Download</a></button>";
        $event->return = $buttons;
      }
    );
  }
}
