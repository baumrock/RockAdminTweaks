<?php

namespace RockAdminTweaks;

class AddModalParam extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Adds &modal=1 to all links in the pagelist when the pagelist is viewed in a modal window',
    ];
  }

  public function init(): void
  {
    $this->loadJS();
  }
}
