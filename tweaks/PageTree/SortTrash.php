<?php namespace RockAdminTweaks;
class SortTrash extends Tweak {

  public function info() {
    return [
      'description' => 'Show recently trashed pages on top of the list',
      'icon' => 'fa-trash-o',
      'notes' => 'See [issue #386](https://github.com/processwire/processwire-requests/issues/386)',
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
