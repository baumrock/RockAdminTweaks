![img](logo.svg)

This module is similar to AOS and packs different tweaks for the PW admin into one module.

## Problem

If you want to tweak the PW backend you have several options. One option is to use AOS which comes with lots of improvements but also comes with bloat and bugs and it is not actively maintained any more - that's why it is no option for me any more, as much as I enjoyed using it.

On the other hand, packing every little tweak you want to apply to the PW backend into a separate module (which might be best solution from a technical point of view) is not easy for beginners and it is not practical as well for advanced users. RATs tries to fill this gap!

## Solution

Unlike AOS this module was built to make it very easy to add `tweaks` in a modular way. Every tweak can consist of either a `JS`, `CSS` or `PHP` file or any combination of those. Tweaks can be organised as you want in folders (at the moment nesting of those folders is not supported).

The long term goal is to move the most important and popular tweaks of AOS into RATs step by step. The module will get bigger and bigger but at the same time (unlike AOS) the complexity of the module will not increase!

If the tweaks shipped with this module are not enough for your needs then you can place your own tweaks in `/site/assets/RockAdminTweaks` and they will automatically be included and handled by RATs.

![img](hr.svg)

## Preview

![img](https://i.imgur.com/MP9YH6i.png)

![img](hr.svg)

## Quickstart

**Adding CSS/JS Tweaks**

Adding new CSS/JS tweaks is as simple as adding a file either in `site/assets/RockAdminTweaks` or in `site/modules/RockAdminTweaks/tweaks`. Once the file is added to your system the tweak will show up in the config of the module and you can enable or disable it there via a checkbox.

Example JS tweak

```js
alert('I am a JS tweak');
```

Example CSS tweak

```CSS
div { border: 1px solid red; }
```

Have a look at the test tweaks here, that you can try out after installation: https://github.com/baumrock/RockAdminTweaks/tree/main/tweaks/Test

**Adding PHP Tweaks**

To add a tweak that does something on the server side or has advanced config options (more about that later) just add a new PHP tweak in one of the above mentioned folders and add the `RockAdminTweaks` namespace:

```php
<?php namespace RockAdminTweaks;
class Foo extends Tweak {
}
```

That's it! You have created your first PHP Tweak! Of course this tweak does not do anything yet, so we add some magic:

```php
<?php namespace RockAdminTweaks;
class Foo extends Tweak {

  public function init() {
    $this->message('Foo tweak init!');
  }

  public function ready() {
    $this->message('Foo tweak ready!');
  }

}
```

As you can see the tweak supports PW's `init()` and `ready()` events. This means it is extremely easy to attach hooks and do really anything with a simple PHP tweak and it's also extremely easy to move them from one project to another but still keep the flexibility of enabling/disabling it per installation.

If you want to see a real world example see the simple `SortTrash` tweak: https://github.com/baumrock/RockAdminTweaks/blob/main/tweaks/PageTree/SortTrash.php

![img](hr.svg)

## Example of a more complex tweak

Tweaks can be as complex as you can imagine and you can easily provide config settings to make your tweak dynamically configurable.

See this example: https://github.com/baumrock/RockAdminTweaks/blob/main/tweaks/Test/Message.php

![img](https://i.imgur.com/5fI7mr3.png)
