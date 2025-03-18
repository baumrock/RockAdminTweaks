<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

use function ProcessWire\wire;

/**
 * Instead of rebuilding image variations, remove them and they'll be rebuilt when next requested
 * Fix for: https://github.com/processwire/processwire-issues/issues/1301
 * Also see:
 * https://github.com/processwire/processwire-issues/issues/1277
 * https://processwire.com/talk/topic/31127-image-variations-not-recreated-after-cropping/
 *
 * Credits: Robin S
 */
class RemoveVariations extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Remove all image variations instead of rebuilding them',
      'infoLink' => 'https://processwire.com/talk/topic/31127--',
    ];
  }

  public function init(): void
  {
    wire()->addHookBefore('Pageimage::rebuildVariations', $this, 'removeVariations');
  }

  public function removeVariations(HookEvent $event)
  {
    /** @var Pageimage $pageimage */
    $pageimage = $event->object;
    $event->replace = true;
    $pageimage->removeVariations();
    // Return expected output to avoid errors
    $event->return = [
      'rebuilt' => [],
      'skipped' => [],
      'reasons' => [],
      'errors' => [],
    ];
  }
}
