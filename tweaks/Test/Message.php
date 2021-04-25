<?php namespace RockAdminTweaks\Test;

use RockAdminTweaks\Tweak;

class Message extends Tweak {

  const message = 'test-message-message';

  public function getConfigFields($fields) {
    $fields->add([
      'type' => 'text',
      'name' => self::message,
      'label' => 'Message to show',
      'value' => $this->rats->config(self::message),
    ]);
    return $fields;
  }

  public function ready() {
    $this->message($this->rats->config(self::message));
  }

}
