# acf-gutenblocks

[![Packagist Version](https://img.shields.io/packagist/v/itinerisltd/acf-gutenblocks.svg)](https://packagist.org/packages/itinerisltd/acf-gutenblocks)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/itinerisltd/acf-gutenblocks.svg)](https://packagist.org/packages/itinerisltd/acf-gutenblocks)
[![Packagist Downloads](https://img.shields.io/packagist/dt/itinerisltd/acf-gutenblocks.svg)](https://packagist.org/packages/itinerisltd/acf-gutenblocks)
[![GitHub License](https://img.shields.io/github/license/itinerisltd/acf-gutenblocks.svg)](https://github.com/ItinerisLtd/acf-gutenblocks/blob/master/LICENSE)
[![Hire Itineris](https://img.shields.io/badge/Hire-Itineris-ff69b4.svg)](https://www.itineris.co.uk/contact/)


Easily create Gutenberg Blocks with Advanced Custom Fields.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Minimum Requirements](#minimum-requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Block definition](#block-definition)
  - [Block constructors](#block-constructors)
    - [`AbstractBlock`](#abstractblock)
    - [`AbstractBladeBlock`](#abstractbladeblock)
- [Template data](#template-data)
- [Fields](#fields)
  - [Simple array](#simple-array)
  - [ACF Builder](#acf-builder)
- [Filters](#filters)
  - [`acf_gutenblocks/blocks` - `(array $blocks)`](#acf_gutenblocksblocks---array-blocks)
  - [`acf_gutenblocks/get_initializables` - `(array $initializables)`](#acf_gutenblocksget_initializables---array-initializables)
  - [`acf_gutenblocks/render_block_frontend_path` - `(string $path, Block $block)`](#acf_gutenblocksrender_block_frontend_path---string-path-block-block)
  - [`acf_gutenblocks/render_block_html_output` - `(string $html, Block $block)`](#acf_gutenblocksrender_block_html_output---string-html-block-block)
  - [`acf_gutenblocks/default_icon` - `(string $icon)`](#acf_gutenblocksdefault_icon---string-icon)
  - [`acf_gutenblocks/block_settings` - `(array $settings, string $name)`](#acf_gutenblocksblock_settings---array-settings-string-name)
- [FAQ](#faq)
  - [Can I use a different template rendering option?](#can-i-use-a-different-template-rendering-option)
  - [Do I need to adhere to any structure or standard?](#do-i-need-to-adhere-to-any-structure-or-standard)
  - [Why not load all Blocks from a given directory? It's much easier!](#why-not-load-all-blocks-from-a-given-directory-its-much-easier)
  - [My Blade template doesn't load.](#my-blade-template-doesnt-load)
- [Author Information](#author-information)
- [Feedback](#feedback)
- [Change log](#change-log)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Minimum Requirements

- [PHP](https://secure.php.net/manual/en/install.php) >= 8.0
- [WordPress](https://wordpress.org/download/) (preferrably [Bedrock](https://roots.io/bedrock/)) >= 5.0
- [Advanced Custom Fields](https://www.advancedcustomfields.com/) >= 5.8.0

## Installation

```bash
$ composer require itinerisltd/acf-gutenblocks
```

## Usage

1. Activate the plugin
2. Create a directory to store your Blocks in your plugin or theme
3. Define your [Block](#block-definition) and frontend template

  ```
  Blocks/
    └── Testimonial/
        ├── views/
        │   └── frontend.php # Block template file
        └── Testimonial.php # Block constructor and controller
  ```

4. Register your Block by appending the Block class name as a string to the `acf_gutenblocks/blocks` filter

```php
use Fully\Qualified\Namespace\Testimonial;
use App\Blocks\Banner;

add_filter('acf_gutenblocks/blocks', function (array $blocks): array {
    $new_blocks = [
        Testimonial::class,
        Banner::class,
    ];
    return array_merge($blocks, $new_blocks);
});
```

## Block definition

Blocks are registered using PHP classes to provide a simple "Controller" to allow separation of logic and functionality from your template. This can really help to isolate and organise code that is intended only for that Block.

To create a Block, you must extend your class from the available Block constructors and pass any valid [`acf_register_block()`](https://www.advancedcustomfields.com/resources/acf_register_block/) arguments to the parent constructor. Here can also define your controller methods for use within your template.

```php
# Blocks/Testimonial/Testimonial.php
<?php

declare(strict_types=1);

namespace App\Blocks\Testimonial;

use Itineris\AcfGutenblocks\AbstractBlock;

class Testimonial extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Testimonial', 'sage'),
            'description' => __('Testimonial description', 'sage'),
            'category' => 'formatting',
            'post_types' => ['post', 'page'],
            // Other valid acf_register_block() settings
        ]);
    }

    /**
     * Make $items available to your template
     */
    public function with(): array
    {
        return [
            'items' => (array) get_field('items'),
        ];
    }
}
```

### Block constructors

#### `AbstractBlock`

Extend from this class to register a vanilla PHP template.

#### `AbstractBladeBlock`

If your project uses the [Sage](https://roots.io/sage) theme, you can take advantage of Blade templating by extending from this class (in future, [Sage](https://roots.io/sage) will be optional).

The `isValid` method will look for `\App\template`. If you're in a Sage environment where that doesn't exist (i.e. Sage 10), you can use the `acf_gutenblocks/blade_engine_callable` filter to return a different callable.

```php
add_filter('acf_gutenblocks/blade_engine_callable', function (string $callable): string {
    return '\Roots\view';
});
```

## Template data

Your Block constructor class is available to your template via `$controller`. This allows you to create truly advanced Blocks by organising all of your functional code and logic into a place where you can take more advantage of an OOP approach.

Additionally, the `with()` method lets you pass variables to your template.
To create a variable for your template, create a key+value pair in the `with()` method:

```php
public function with(): array
{
    return [
        'items' => (array) get_field('items'),
    ];
}
```

Using `$items` in your template:

```php
# Blocks/Testimonial/views/frontend.php
<?php foreach ($items as $item) : ?>
    <p><?php echo $item['title']; ?></p>
<?php endforeach; ?>
```

## Fields

You can define your ACF fields in your Block by returning an array of fields in the `registerFields` method.

### Simple array

Read more [here](https://www.advancedcustomfields.com/resources/register-fields-via-php/#example).

```php
protected function registerFields(): array
{
    return [
        // Any valid field settings
    ];
}
```

### ACF Builder

```php
protected function registerFields(): array
{
    $testimonial = new FieldsBuilder('testimonial');

    $testimonial
        ->setLocation('block', '==', 'acf/testimonial');

    $testimonial
        ->addText('quote')
        ->addText('cite')
        ->addRepeater('list_items')
            ->addText('list_item')
            ->addTrueFalse('enabled', [
                'ui' => 1,
                'default_value' => 1,
            ])
        ->endRepeater();

    return $testimonial->build();
}
```

## Filters

### `acf_gutenblocks/blocks` - `(array $blocks)`

The Block Loader. Use this to load and register your Block classes.

### `acf_gutenblocks/get_initializables` - `(array $initializables)`

Called before looping Blocks and checking if they are valid to load.

### `acf_gutenblocks/render_block_frontend_path` - `(string $path, Block $block)`

Used to change the frontend view path.

### `acf_gutenblocks/render_block_html_output` - `(string $html, Block $block)`

For use with `AbstractBlock`. Allows manipulating the frontend view HTML after being included.

### `acf_gutenblocks/default_icon` - `(string $icon)`

Used to change the default icon.

### `acf_gutenblocks/block_settings` - `(array $settings, string $name)`

Change the ACF Block settings registered in the Block before initialising it.

## FAQ

### Can I use a different template rendering option?

You could make a copy of `AbstractBlock`, rename it and define your own `renderBlockCallback` method. Just make sure your Block class extends from it.

### Do I need to adhere to any structure or standard?

You can manage your Blocks any way you wish. This README will use our [preferred approach](#usage) of strict typing and the directory structure.

### Why not load all Blocks from a given directory? It's much easier!

Using directory scanning options like `glob` and `DirectoryIterator` (or other Iterators) will have a performance impact within your application.
There are many reasons for that, but the most simple ones are that they take arguments that must be read and dealt with before getting to the actual directory scanning.

Manually loading your Blocks also means that you as a developer are more aware of what you are loading and can do things like conditional logic of loading your Blocks.

### My Blade template doesn't load.

Check your PHP error logs and that your installation is [valid](https://github.com/ItinerisLtd/acf-gutenblocks/blob/fe06055e1d0c48c6c0837586042e1146d3d6a8a8/src/AbstractBladeBlock.php#L16-L19) for use with [Sage](https://roots.io/sage).

## Author Information

[acf-gutenblocks](https://github.com/ItinerisLtd/acf-gutenblocks) is a [Itineris Limited](https://www.itineris.co.uk/) project created by [Lee Hanbury-Pickett](https://github.com/codepuncher).

Shout out to [@nicoprat](https://github.com/nicooprat) with his [article](https://medium.com/nicooprat/acf-blocks-avec-gutenberg-et-sage-d8c20dab6270) which kickstarted this.

Thanks to [@mmirus](https://github.com/mmirus/) for pointers and giving me the idea for this package.

Full list of contributors can be found [here](https://github.com/ItinerisLtd/acf-gutenblocks/graphs/contributors).

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please submit an [issue](https://github.com/ItinerisLtd/acf-gutenblocks/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## License

[acf-gutenblocks](https://github.com/ItinerisLtd/acf-gutenblocks) is released under the [MIT License](https://opensource.org/licenses/MIT).
