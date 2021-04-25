<?php namespace RockAdminTweaks;
class Message extends Tweak {

  const message = 'test-message-message';

  public function info() {
    return [
      'description' => 'Show Message',
      'icon' => 'bullhorn',
    ];
  }

  public function getConfigFields($fields) {
    $fields->add([
      'type' => 'text',
      'name' => self::message,
      'label' => 'Message to show',
      'value' => $this->rats()->config(self::message),
      'required' => true,
    ]);
    return $fields;
  }

  public function ready() {
    $this->message($this->rats()->config(self::message));
  }

}
