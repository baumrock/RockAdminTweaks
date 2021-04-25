<?php namespace RockAdminTweaks;

use ProcessWire\Inputfield;
use ProcessWire\InputfieldFieldset;
use ProcessWire\InputfieldWrapper;
use ProcessWire\RockAdminTweaks;
use ProcessWire\Wire;

class Tweak extends Wire {

  public function info() {}

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
    $fs->notes = $this->getInfo('notes');
    $fs->add([
      'type' => 'checkbox',
      'entityEncodeLabel' => false,
      'label' => "Enabled <small class='uk-text-xsmall uk-margin-small-left uk-text-muted'>{$this->configName}</small>",
      'name' => $this->configName,
      'checked' => $this->isEnabled() ? 'checked' : '',
    ]);
    if($this->isReadonly()) {
      $fs->children()->last()->attr('disabled', 'disabled');
    }

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
      'description' => $this->configName(),
      'icon' => 'code',
      'loadCSS' => true,
      'loadJS' => true,
    ], $this->info() ?: []);
    if(!array_key_exists($property, $info)) return '';
    return $info[$property];
  }

  /**
   * Is this tweak enabled?
   * @return bool
   */
  public function isEnabled() {
    return !!$this->rats()->config($this->configName);
  }

  /**
   * Is the checkbox for this tweak readonly?
   * @return bool
   */
  public function isReadonly() {
    return false;
  }

  /**
   * Load css of this tweak?
   * @return bool
   */
  public function loadCSS($page) {
    if(!$this->isEnabled()) return false;
    $load = $this->getInfo('loadCSS');
    if(is_string($load)) $load = $page->matches($load);
    return !!$load;
  }

  /**
   * Load js of this tweak?
   * @return bool
   */
  public function loadJS($page) {
    if(!$this->isEnabled()) return false;
    $load = $this->getInfo('loadJS');
    if(is_string($load)) $load = $page->matches($load);
    return !!$load;
  }

  public function rats(): RockAdminTweaks {
    return $this->wire->modules->get("RockAdminTweaks");
  }

  /**
   * Get url of file
   * @return string|false
   */
  public function url($ext) {
    $config = $this->wire->config;
    $file = $this->path.".$ext";
    if(!is_file($file)) return false;
    $url = str_replace($config->paths->root, $config->urls->root, $file);
    return "$url?m=".filemtime($file);
  }

  public function __debugInfo() {
    return [
      'name' => $this->name,
      'configName' => $this->configName(),
      'path' => $this->path,
    ];
  }
}
