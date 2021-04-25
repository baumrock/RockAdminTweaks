<?php namespace RockAdminTweaks;

use ProcessWire\Wire;

abstract class Tweak extends Wire {

  public function info() {
    return [
      'description' => 'No description for this tweak',
      'icon' => 'bolt',
      'foo' => 'bar',
    ];
  }
}
