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
        - [AbstractBlock](#abstractblock)
        - [AbstractBladeBlock](#abstractbladeblock)
- [Controller](#controller)
- [Fields](#fields)
- [FAQ](#faq)
  - [Can I use a different template rendering option?](#can-i-use-a-different-template-rendering-option)
  - [Do I need to adhere to any structure or standard?](#do-i-need-to-adhere-to-any-structure-or-standard)
- [Author Information](#author-information)
- [Feedback](#feedback)
- [Change log](#change-log)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Minimum Requirements

- [PHP](https://secure.php.net/manual/en/install.php) >= 7.1
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

4. Register your Block by appending the Block class name as a string to the `acf_gutenblock_builder/blocks` filter

```php
add_filter('acf_gutenblock_builder/blocks', function (array $blocks): array {
    $new_blocks = [
        Testimonial::class,
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
            'title' => __('Testimonial', 'fabric'),
            'description' => __('Testimonial description', 'fabric'),
            'category' => 'formatting',
            // Other valid acf_register_block() settings
        ]);
    }

    public function getItems(): array
    {
        $items = [];
        foreach (get_field('list_items') as $item) {
            if ($item['enabled']) {
                $items[] = $item['list_item'];
            }
        }
        return $items;
    }
}
```

### Block constructors

#### `AbstractBlock`

Extend from this class to register a vanilla PHP template.

#### `AbstractBladeBlock`

If your project uses the [Sage](https://roots.io/sage) theme, you can take advantage of Blade templating by extending from this class (in future, [Sage](https://roots.io/sage) will be optional).

## Controller

Your Block constructor class is available to your template via `$controller`. This allows you to create truly advanced Blocks by organising all of your functional code and logic into a place where you can take more advantage of an OOP approach.

In the [Block definition](#block-definition) example in this page, we have the `getItems` method which can be used in the template like so:

```php
# Blocks/Testimonial/views/frontend.php
<?php foreach ($controller->getItems() as $item) : ?>
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

## FAQ

### Can I use a different template rendering option?

You could make a copy of `AbstractBlock`, rename it and define your own `renderBlockCallback` method. Just make sure your Block class extends from it.

### Do I need to adhere to any structure or standard?

You can manage your Blocks any way you wish. This README will use our [preferred approach](#usage) of strict typing and the directory structure.

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
