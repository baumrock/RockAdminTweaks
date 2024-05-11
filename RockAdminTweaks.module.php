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
      'autoload' => 'admin',
      'singular' => true,
      'icon' => 'magic',
      'requires' => [],
      'installs' => [],
    ];
  }

  public function init() {
    $this->wire->set('rats', $this);
    $this->assets = $this->wire->config->paths->assets.$this->className."/";
    $this->dirs = [
      Paths::normalizeSeparators(__DIR__."/tweaks/"),
      $this->assets,
    ];
    $this->loadTweaks();
    foreach($this->tweaks->find("enabled=1,hasInit=1") as $tweak) $tweak->init();
  }

  public function ready() {
    // trigger ready()
    foreach($this->tweaks->find("enabled=1,hasReady=1") as $tweak) {
      $tweak->ready();
    }

    // load styles
    foreach($this->tweaks as $tweak) {
      /** @var Tweak $tweak */
      if(!$tweak->loadCSS($this->wire->page)) continue;
      $this->wire->config->styles->add($tweak->url('css'));
    }

    // load scripts
    foreach($this->tweaks as $tweak) {
      /** @var Tweak $tweak */
      if(!$tweak->loadJS($this->wire->page)) continue;
      $this->wire->config->scripts->add($tweak->url('js'));
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
    require_once(__DIR__."/Tweak.php");
    $this->tweaks = $this->wire(new WireArray());
    foreach($this->findFiles() as $file) {
      $tweak = $this->getTweak($file);
      if($this->tweaks->has($tweak)) continue;
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
        $fs->add($tweak->configWrapper());
      }
      $inputfields->add($fs);
    }

    return $inputfields;
  }

  /**
   * Get tweak object from filename
   * If a PHP file exists we load the dedicated PHP file
   * Otherwise we load the default tweak
   * @return Tweak|null
   */
  public function getTweak($file) {
    if(!is_file($file)) return;
    $config = $this->wire->config;
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $noExt = substr($file, 0, -strlen(".$ext"));
    $name = str_replace($this->dirs, "", $noExt);
    $filename = pathinfo($file, PATHINFO_FILENAME);
    if($tweak = $this->tweaks->get($name)) return $tweak;
    if(is_file($noExt.".php")) {
      require_once($file);
      $class = "RockAdminTweaks\\".str_replace("/", "\\", $filename);
      $tweak = new $class();
    }
    else $tweak = new Tweak();
    $tweak->name = $name;
    $tweak->path = $noExt;
    $tweak->configName = $tweak->configName();
    $tweak->enabled = $tweak->isEnabled();
    $tweak->hasInit = method_exists($tweak, "init");
    $tweak->hasReady = method_exists($tweak, "ready");
    return $tweak;
  }

  public function ___install() {
    $this->wire->files->mkdir($this->wire->config->paths->assets.$this->className);
  }
}
