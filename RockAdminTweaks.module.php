<?php namespace ProcessWire;

use RockAdminTweaks\Tweak;

/**
 * @author Bernhard Baumrock, 25.04.2021
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockAdminTweaks extends WireData implements Module, ConfigurableModule {

  /** @var array */
  private $array = [];

  /** @var string */
  public $assets;

  /** @var array */
  public $dirs;

  /** @var WireArray */
  public $tweaks;

  public static function getModuleInfo() {
    return [
      'title' => 'RockAdminTweaks',
      'version' => '0.0.1',
      'summary' => 'Tweaks for the ProcessWire admin',
      'autoload' => true,
      'singular' => true,
      'icon' => 'bolt',
      'requires' => [],
      'installs' => [],
    ];
  }

  public function init() {
    $this->assets = $this->wire->config->paths->assets.$this->className."/";
    $this->dirs = [
      Paths::normalizeSeparators(__DIR__."/tweaks/"),
      $this->assets,
    ];
    $this->loadTweaks();
    foreach($this->tweaks->find("enabled=1,hasInit=1") as $tweak) $tweak->init();
  }

  public function ready() {
    if($this->wire->page->template != 'admin') return;

    // trigger ready()
    foreach($this->tweaks->find("enabled=1,hasReady=1") as $tweak) $tweak->ready();

    // load styles
    foreach($this->tweaks->find("ext=css") as $tweak) {
      $this->wire->config->styles->add($tweak->url);
    }
  }

  /**
   * Add tweak to grouped array of tweaks
   * @return array
   */
  public function addToArray($tweak) {
    $array = $this->array;
    $parts = explode("/", $tweak->name);
    if(count($parts) === 1) $folder = 'Global';
    else $folder = $parts[0];
    $array[$folder][] = $tweak;
    $this->array = $array;
  }

  /**
   * Get config property
   * @return mixed
   */
  public function config($property) {
    return $this->$property;
  }

  /**
   * Find tweak files
   * @return array
   */
  public function findFiles($ext = null) {
    $options = [];
    if(is_string($ext)) $options = ['extensions'=>[$ext]];
    $files = [];
    foreach($this->dirs as $dir) {
      $found = $this->wire->files->find($dir, $options);
      $files = array_merge($files, $found);
    }
    return $files;
  }

  /**
   * Load all tweaks
   */
  public function loadTweaks() {
    $config = $this->wire->config;
    require_once(__DIR__."/Tweak.php");
    $this->tweaks = $this->wire(new WireArray());
    foreach($this->dirs as $dir) {
      $this->wire->classLoader->addNamespace("RockAdminTweaks", $dir);
    }
    foreach($this->findFiles() as $file) {
      $ext = pathinfo($file, PATHINFO_EXTENSION);
      $name = substr(str_replace($this->dirs, "", $file), 0, -strlen($ext)-1);
      if($ext == 'php') {
        $class = "RockAdminTweaks\\".str_replace("/", "\\", $name);
        $tweak = new $class();
      }
      else $tweak = new Tweak();
      $tweak->name = $name;
      $tweak->configName = $tweak->configName();
      $tweak->enabled = $tweak->isEnabled();
      $tweak->hasInit = method_exists($tweak, "init");
      $tweak->hasReady = method_exists($tweak, "ready");
      $tweak->path = $file;
      $tweak->ext = $ext;
      $this->tweaks->add($tweak);
      $this->addToArray($tweak);
    }
  }

  /**
  * Config inputfields
  * @param InputfieldWrapper $inputfields
  */
  public function getModuleConfigInputfields($inputfields) {
    // add fields for all tweaks
    foreach($this->array as $folder => $tweaks) {
      $fs = $this->wire(new InputfieldFieldset()); /** @var InputfieldFieldset $f */
      $fs->label = $folder;
      foreach($tweaks as $tweak) {
        if($tweak->ext !== 'php') continue;
        $fs->add($tweak->configWrapper());
      }
      $inputfields->add($fs);
    }

    return $inputfields;
  }

  public function ___install() {
    $this->wire->files->mkdir($this->wire->config->paths->assets.$this->className);
  }
}
