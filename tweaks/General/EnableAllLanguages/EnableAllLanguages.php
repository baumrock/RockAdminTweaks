<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

use function ProcessWire\wire;

class EnableAllLanguages extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Enable all languages after a new page has been added.',
    ];
  }

  public function init(): void
  {
    $this->wire->addHookBefore('Pages::added', $this, 'enableLanguages');
  }

  /**
   * Enable all languages for all created pages
   * @param HookEvent $event
   * @return void
   * @throws WireException
   */
  protected function enableLanguages(HookEvent $event): void
  {
    if (!wire()->languages) return;
    $p = $event->arguments(0);
    foreach (wire()->languages->findNonDefault() as $language) {
      $p->setLanguageStatus($language, true);
    }
    $p->save();
  }
}
