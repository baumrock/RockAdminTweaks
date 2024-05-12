<?php

namespace ProcessWire;

$info = [
  'title' => 'RockAdminTweaks',
  'version' => json_decode(file_get_contents(__DIR__ . '/package.json'))->version,
  'summary' => 'Tweaks for the ProcessWire Backend.',
  'autoload' => 'template=admin',
  'singular' => true,
  'icon' => 'magic',
  'requires' => [
    'PHP>=8.1',
  ],
];
