<?php namespace RockAdminTweaks\Test;
class RedDivs extends \RockAdminTweaks\Tweak {
  public function ready() {
    $this->wire->config->styles->add($this->url('css'));
  }
}
