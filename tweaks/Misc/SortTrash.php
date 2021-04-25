<?php namespace RockAdminTweaks\Misc;

use RockAdminTweaks\Tweak;

class SortTrash extends Tweak {

  public function info() {
    return [
      'description' => 'Show recently trashed pages on top of the list',
      'icon' => 'trash-o',
    ];
  }

  public function init() {
    $this->message("SortTrash.php");
  }

  public function ready() {
    $this->message("SortTrash is ready: ".$this->wire->page->process);
  }

}
