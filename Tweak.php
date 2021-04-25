<?php namespace RockAdminTweaks;

use ProcessWire\Inputfield;
use ProcessWire\InputfieldFieldset;
use ProcessWire\InputfieldWrapper;
use ProcessWire\RockAdminTweaks;
use ProcessWire\Wire;

class Tweak extends Wire {

  /** @var RockAdminTweaks $ */
  public $rats;

  public function __construct() {
    $this->rats = $this->wire->modules->get("RockAdminTweaks");
  }

  public function info() {}

  public function ready() {
    bd('default tweak ready', $this->name);
  }

  /**
   * Return name of config property
   * @return string
   */
  public function configName() {
    return $this->wire->sanitizer->selectorField($this->name);
  }

  /**
   * Get config fields for this tweak
   * @return
   */
  final public function configWrapper() {
    $fs = $this->wire(new InputfieldFieldset()); /** @var InputfieldFieldset $f */
    $fs->label = $this->getInfo('description');
    $fs->icon = $this->getInfo('icon');
    $fs->add([
      'type' => 'checkbox',
      'entityEncodeLabel' => false,
      'label' => "Enabled <small class='uk-text-xsmall uk-margin-small-left uk-text-muted'>{$this->configName}</small>",
      'name' => $this->configName,
      'checked' => $this->isEnabled() ? 'checked' : '',
    ]);
    // bd($fs->children()->last()->attr('disabled', 'disabled'));
    $fs->notes = $this->url;

    // add config fields
    $wrapper = $this->wire(new InputfieldWrapper()); /** @var InputfieldWrapper $wrapper */
    $fields = $this->getConfigFields($wrapper);
    foreach($fields as $field) $field->showIf = $this->configName."=1";
    $fs->add($fields);

    return $fs;
  }

  /**
   * Get fields for configuration of this tweak
   */
  public function getConfigFields(InputfieldWrapper $fields) {
    return $fields;
  }

  /**
   * Get info value
   */
  public function getInfo($property) {
    $info = array_merge([
      'label' => $this->className,
      'description' => $this->className,
      'icon' => 'bolt',
    ], $this->info() ?: []);
    if(!array_key_exists($property, $info)) return '';
    return $info[$property];
  }

  /**
   * Is this tweak enabled?
   * @return bool
   */
  public function isEnabled() {
    return !!$this->rats->config($this->configName);
  }

  /**
   * Get url of file
   * @return string
   */
  public function url($ext) {
    $config = $this->wire->config;
    $file = substr($this->path, 0, -strlen($this->ext)-1).".$ext";
    $url = str_replace($config->paths->root, $config->urls->root, $file);
    if(is_file($file)) $url .= "?m=".filemtime($file);
    return $url;
  }

  public function __debugInfo() {
    return [
      'name' => $this->name,
      'config' => $this->config,
      'path' => $this->path,
      'url' => $this->url,
      'ext' => $this->ext,
    ];
  }
}
