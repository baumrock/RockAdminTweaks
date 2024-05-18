<?php

namespace RockAdminTweaks;

class FullRowHover extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Show pagelist actions on full row hover',
    ];
  }

  public function ready(): void
  {
    $this->loadCSS();
  }
}
