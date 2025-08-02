<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

class FakeFavicon extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Adds a fake favicon to the backend to prevent 404 in devtools.',
    ];
  }

  public function init(): void
  {
    $this->wire->addHookAfter('AdminTheme::getExtraMarkup', $this, 'addFavicon');
  }

  public function addFavicon(HookEvent $event)
  {
    $parts = $event->return;
    $parts['head'] .= '<link rel="icon" href="data:,">';
    $event->return = $parts;
  }
}
