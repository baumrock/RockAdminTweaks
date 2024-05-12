# Assets

Tweaks can load JS and CSS assets. As you might want to load assets based on some conditions we do not automatically load them. But it's only one line of code to do so and it will automatically take care of cache busting:

```php
// the tweak CopyFieldNames.php will load
// the js file CopyFieldNames.js
$this->loadJS();
```

The only thing you have to define is when to load the script, for example:

```php
public function init(): void
{
  if (!$this->wire->user->isSuperuser()) return;
  wire()->addHookBefore(
    'ProcessController::execute',
    function () {
      $this->loadJS();
    }
  );
}

// or shorter:
public function init(): void
{
  if (!$this->wire->user->isSuperuser()) return;
  wire()->addHookBefore('ProcessController::execute', $this, 'loadJS');
}
```
