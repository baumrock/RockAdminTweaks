# RockAdminTweaks

![img](https://i.imgur.com/MP9YH6i.png)

## Quickstart

**Adding CSS/JS Tweaks**

Adding new CSS/JS tweaks is as simple as adding a file either in `site/assets/RockAdminTweaks` or in `site/modules/RockAdminTweaks/tweaks`. Once the file is added to your system the tweak will show up in the config of the module and you can enable or disable it there via a checkbox.

**Adding PHP Tweaks**

To add a tweak that does something on the server side or has advanced config options (more about that later) just add a new PHP tweak in one of the above mentiones folders. Note that you need to provide the correct namespace that is based on PSR-4: `/site/assets/RockAdminTweaks/Demo/Foo.php`

```php
<?php namespace RockAdminTweaks\Demo;
class Foo extends \RockAdminTweaks\Tweak {
}
```

That's it! You have created your first PHP Tweak! Of course this tweak does not do anything yet, so we add some magic:

```php
<?php namespace RockAdminTweaks\Demo;
class Foo extends \RockAdminTweaks\Tweak {

  public function init() {
    $this->message('Foo tweak init!');
  }

  public function ready() {
    $this->message('Foo tweak ready!');
  }

}
```

As you can see the tweak supports PW's `init()` and `ready()` events. This means it is extremely easy to attach hooks and do really anything with a simple PHP tweak and it's also extremely easy to move them from one project to another but still keep the flexibility of enabling/disabling it per installation.
