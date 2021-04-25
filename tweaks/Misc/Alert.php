<?php namespace RockAdminTweaks\Misc;

use RockAdminTweaks\Tweak;

class Alert extends Tweak {

  public function info() {
    return [
      'description' => 'Show alert message',
      'icon' => 'trash-o',
    ];
  }

  public function init() {
    $this->message('Alert.php');
  }

}
