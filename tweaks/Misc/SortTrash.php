<?php namespace RockAdminTweaks\Misc;

use RockAdminTweaks\Tweak;

class SortTrash extends Tweak {

  public function info() {
    return [
      'description' => 'Show recently trashed pages on top of the list',
      'icon' => 'fa-trash-o',
    ];
  }

  public function init() {
    $this->addHookAfter("ProcessPageList::find", function($event) {
      $selector = $event->arguments(0);
      $page = $event->arguments(1);
      if($page->id !== $event->config->trashPageID) return;
      $event->return = $page->children($selector.",sort=-modified");
    });
  }

}
