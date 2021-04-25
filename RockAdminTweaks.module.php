<?php namespace ProcessWire;
/**
 * @author Bernhard Baumrock, 25.04.2021
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockAdminTweaks extends WireData implements Module, ConfigurableModule {

  /** @var string */
  public $assets;

  /** @var array */
  public $dirs;

  /** @var WireArray */
  private $ready;

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
    $this->ready = $this->wire(new WireArray());
    $this->loadTweaks();
  }

  public function ready() {
    foreach($this->ready as $tweak) $tweak->ready();
  }

  /**
   * Find tweak files
   * @return array
   */
  public function findFiles($ext = null) {
    if(is_string($ext)) $ext = ['extensions'=>[$ext]];
    $files = [];
    foreach($this->dirs as $dir) {
      $found = $this->wire->files->find($dir, $ext ?: []);
      $files = array_merge($files, $found);
    }
    return $files;
  }

  /**
   * Load all tweaks
   */
  public function loadTweaks() {
    require_once(__DIR__."/Tweak.php");
    foreach($this->dirs as $dir) {
      $this->wire->classLoader->addNamespace("RockAdminTweaks", $dir);
    }
    foreach($this->findFiles("php") as $file) {
      $class = str_replace($this->dirs, "", $file);
      $class = substr("RockAdminTweaks\\".str_replace("/", "\\", $class), 0, -4);
      $tmp = new $class();
      if(method_exists($tmp, "init")) $tmp->init();
      if(method_exists($tmp, "ready")) $this->ready->add($tmp);
    }
  }

  /**
  * Config inputfields
  * @param InputfieldWrapper $inputfields
  */
  public function getModuleConfigInputfields($inputfields) {
    return $inputfields;
  }

  public function ___install() {
    $this->wire->files->mkdir($this->wire->config->paths->assets.$this->className);
  }
}
