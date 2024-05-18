<?php

namespace RockAdminTweaks;

use ProcessWire\Wire;
use ProcessWire\WireData;

use function ProcessWire\wire;

abstract class Tweak extends Wire
{
  /**
   * Path to php file of this tweak
   * @var string
   */
  public $file;

  /**
   * Id for use in dom markup (folder-name)
   * @var string
   */
  public $id;

  /**
   * WireData object of info() array
   * @var WireData
   */
  public $info;

  /**
   * Tweak key (Folder:Name)
   * @var string
   */
  public $key;

  /**
   * Tweak Name
   * @var string
   */
  public $name;

  public function __construct($key)
  {
    $this->info = new WireData();
    $this->info->setArray($this->info());
    $this->key = $key;
    $this->id = wire()->sanitizer->pageNameUTF8($key);
  }

  public function info(): array
  {
    return [];
  }

  public function init(): void
  {
  }

  public function ready(): void
  {
  }


  public final function loadCSS(): void
  {
    $cssfile = substr($this->file, 0, -3) . "css";
    $url = wire()->config->versionUrl($this->pathToUrl($cssfile));
    wire()->config->styles->add($url);
  }

  public final function loadJS(): void
  {
    $jsfile = substr($this->file, 0, -3) . "js";
    $url = wire()->config->versionUrl($this->pathToUrl($jsfile));
    wire()->config->scripts->add($url);
  }

  public function pathToUrl($path): string
  {
    return str_replace(
      wire()->config->paths->root,
      wire()->config->urls->root,
      $path
    );
  }

  public function __debugInfo()
  {
    return [
      'name' => $this->name,
      'key' => $this->key,
      'id' => $this->id,
      'file' => $this->file,
    ];
  }
}
