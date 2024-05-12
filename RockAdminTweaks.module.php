<?php

namespace ProcessWire;

use RockAdminTweaks\Tweak;

/**
 * @author Bernhard Baumrock, 25.04.2021
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockAdminTweaks extends WireData implements Module, ConfigurableModule
{
  const cacheName = "RockAdminTweaks";

  private $enabledTweaks = [];

  public $tweakPathTemplates;
  public $tweakPathModules;

  public $tweakArray = [];

  public $tweaks;

  public function init(): void
  {
    $this->tweaks = new WireArray();
    $this->wire->classLoader->addNamespace("RockAdminTweaks", __DIR__ . "/classes");
    $this->tweakPathTemplates = $this->wire->config->paths->templates . $this->className . "/";
    $this->tweakPathModules = __DIR__ . "/tweaks/";
    $this->loadTweakArray();
    $this->loadEnabledTweaks();
  }

  public function ready(): void
  {
    foreach ($this->tweaks as $tweak) $tweak->ready();
  }

  private function loadEnabledTweaks(): void
  {
    $this->enabledTweaks = $this->wire->cache->get(self::cacheName) ?: [];
    foreach ($this->enabledTweaks as $key) {
      $tweak = $this->loadTweak($key);
      $this->tweaks->add($tweak);
      $tweak->init();
    }
  }

  private function loadTweak($key)
  {
    if (!array_key_exists($key, $this->tweakArray)) return;
    $file = $this->tweakArray[$key];
    if (!is_file($file)) return;
    require_once $file;
    try {
      $parts = explode(":", $key);
      $tweakName = $parts[1];
      $class = "\\RockAdminTweaks\\$tweakName";
      $tweak = new $class();
      $tweak->file = $file;
      $tweak->name = $tweakName;
      return $tweak;
    } catch (\Throwable $th) {
      $this->error($th->getMessage());
    }
  }

  private function loadTweakArray(): void
  {
    $arr = [];
    foreach ([
      $this->tweakPathModules,
      $this->tweakPathTemplates,
    ] as $dir) {
      $files = $this->wire->files->find($dir, [
        'extensions' => ['php'],
      ]);
      foreach ($files as $file) {
        $folder = basename(dirname($file));
        $name = substr(basename($file), 0, -4);
        $arr["$folder:$name"] = $file;
      }
    }
    $this->tweakArray = $arr;
  }

  /* ##### module methods ##### */

  /**
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
    $this->moduleConfigAdd($inputfields);
    $this->moduleConfigTweaks($inputfields);
    return $inputfields;
  }

  private function infoIcon($text): string
  {
    $text = $this->wire->sanitizer->entities($text);
    return "<i class='fa fa-info-circle uk-margin-small-left' title='$text' uk-tooltip></i>";
  }

  public function ___install()
  {
    $this->init();
    $this->wire->files->mkdir($this->tweakPathTemplates);
  }

  public function isEnabled($key): bool
  {
    return in_array($key, $this->enabledTweaks);
  }

  private function moduleConfigAdd(&$inputfields): void
  {
    $path = $this->tweakPathTemplates;

    $fs = new InputfieldFieldset();
    $fs->label = 'Create a new Tweak';
    $fs->icon = 'plus';
    $fs->collapsed = Inputfield::collapsedYes;
    $fs->notes = "The tweak will be created in $path";
    if (!is_writable($path)) $fs->notes .= "\nWARNING: Folder is not writable!";
    $inputfields->add($fs);

    if ($this->wire->config->debug) {
      $fs->add([
        'type' => 'text',
        'name' => 'tgroup',
        'label' => 'Group',
        'columnWidth' => 50,
      ]);
      $fs->add([
        'type' => 'text',
        'name' => 'tname',
        'label' => 'Name',
        'columnWidth' => 50,
      ]);
    } else {
      $fs->add([
        'type' => 'markup',
        'value' => 'This is only allowed if $config->debug = true;',
      ]);
    }
  }

  private function moduleConfigTweaks(&$inputfields): void
  {
    // save enabled tweaks to cache
    if ($this->wire->input->post->submit_save_module) {
      $enabledTweaks = $this->wire->input->post->tweaks;
      $this->wire->cache->save(self::cacheName, $enabledTweaks);
    }

    $fs = new InputfieldFieldset();
    $fs->label = 'Tweaks';
    $fs->icon = 'magic';
    $inputfields->add($fs);

    $oldFolder = false;
    foreach ($this->tweakArray as $key => $path) {
      $parts = explode(":", $key);
      $folder = $parts[0];
      $tweakName = $parts[1];

      // create new folder-field
      if ($oldFolder !== $folder) {
        $f = new InputfieldCheckboxes();
        $f->name = 'tweaks';
        $f->label = $folder;
        $f->icon = 'folder-open-o';
        $f->entityEncodeText = false;
        $fs->add($f);
      }

      // load tweak from file
      $tweak = $this->loadTweak($key);
      $desc = $tweak->info->description;
      if ($desc) $desc = " - $desc";

      // debug
      // bd($tweak);

      // add option as checkbox
      $f->addOption($key, "<strong>$tweakName</strong>$desc", [
        'checked' => $this->isEnabled($key) ? 'checked' : '',
      ]);

      $oldFolder = $folder;
    }
  }
}
