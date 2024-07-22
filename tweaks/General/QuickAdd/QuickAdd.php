<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

class QuickAdd extends Tweak
{
  public function info(): array
  {
    return [
      'description' => 'Skip template selection on page add if only a single template is allowed.',
    ];
  }

  public function init(): void
  {
    $this->wire->addHookBefore('ProcessPageAdd::buildForm', $this, 'skipAdd');
  }

  public function skipAdd(HookEvent $event)
  {
    // this prevents the hook to run on ProcessUser
    if ($event->process != 'ProcessPageAdd') return;
    $templates = $event->process->getAllowedTemplates();
    if (count($templates) !== 1) return;
    foreach ($templates as $k => $tpl) {
      $p = $this->wire->pages->newPage($tpl);
      $p->parent = $this->wire->input->get('parent_id', 'int');
      $p->addStatus('unpublished');
      $p->save();
      $this->wire->session->redirect($p->editUrl());
    }
  }
}
