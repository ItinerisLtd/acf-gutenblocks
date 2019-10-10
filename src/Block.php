<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

class Block
{
    /**
     * The directory name of the block.
     *
     * @since 0.1.0
     * @var string $name
     */
    protected $name = '';

    /**
     * The display name of the block.
     *
     * @since 0.1.0
     * @var string $title
     */
    protected $title = '';

    /**
     * The description of the block.
     *
     * @since 0.1.0
     * @var string $description
     */
    protected $description;

    /**
     * The category this block belongs to.
     *
     * @since 0.1.0
     * @var string $category
     */
    protected $category;

    /**
     * The icon of this block.
     *
     * @since 0.1.0
     * @var string $icon
     */
    protected $icon = '';

    /**
     * An array of keywords the block will be found under.
     *
     * @since 0.1.0
     * @var array $keywords
     */
    protected $keywords = [];

    /**
     * An array of Post Types the block will be available to.
     *
     * @since 0.1.0
     * @var array $post_types
     */
    protected $post_types = ['post', 'page'];

    /**
     * The default display mode of the block that is shown to the user.
     *
     * @since 0.1.0
     * @var string $mode
     */
    protected $mode = 'preview';

    /**
     * The block alignment class.
     *
     * @since 0.1.0
     * @var string $align
     */
    protected $align = '';

    /**
     * Features supported by the block.
     *
     * @since 0.1.0
     * @var array $supports
     */
    protected $supports = [];

    /**
     * The blocks directory path.
     *
     * @since 0.1.0
     * @var string $dir
     */
    public $dir;

    /**
     * The blocks accessibility.
     *
     * @since 0.1.0
     * @var boolean $enabled
     */
    protected $enabled = true;

    /**
     * Begin block construction!
     *
     * @since 0.10
     * @param array $settings The block definitions.
     */
    public function __construct(array $settings)
    {
        // Path related definitions.
        $reflection     = new \ReflectionClass($this);
        $block_path     = $reflection->getFileName();
        $directory_path = dirname($block_path);
        $this->name     = Util::camelToKebab(basename($block_path, '.php'));

        // User definitions.
        $this->enabled = $settings['enabled'] ?? true;
        $this->dir     = $settings['dir'] ?? $directory_path;
        $this->icon    = $settings['icon'] ?? apply_filters('acf_gutenblocks/default_icon', 'admin-generic');

        $settings = apply_filters('acf_gutenblocks/block_settings', [
            'title'       => $settings['title'],
            'description' => $settings['description'],
            'category'    => $settings['category'],
            'icon'        => $this->icon,
            'supports'    => $this->supports,
            'post_types'  => $settings['post_types'] ?? $this->post_types,
        ], $this->name);

        $this->title       = $settings['title'];
        $this->description = $settings['description'];
        $this->category    = $settings['category'];
        $this->icon        = $settings['icon'];
        $this->supports    = $settings['supports'];
        $this->post_types  = $settings['post_types'];

        // Set ACF Fields to the block.
        $this->fields = $this->registerFields();
    }

    /**
     * Is the block enabled?
     *
     * @since 0.1.0
     * @return boolean
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * User defined ACF fields
     *
     * @since 0.1.0
     * @return array
     */
    protected function registerFields(): array
    {
        return [];
    }

    /**
     * Get the block ACF fields
     *
     * @since 0.1.0
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the block name
     *
     * @since 0.1.0
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the block title
     *
     * @since 0.1.0
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the block description
     *
     * @since 0.1.0
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the block category
     *
     * @since 0.1.0
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get the block icon
     *
     * @since 0.1.0
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the block keywords
     *
     * @since 0.1.0
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * Get the block post types
     *
     * @since 0.1.0
     * @return array
     */
    public function getPostTypes(): array
    {
        return $this->post_types;
    }

    /**
     * Get the block mode
     *
     * @since 0.1.0
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get the block alignment
     *
     * @since 0.1.0
     * @return string
     */
    public function getAlignment(): string
    {
        return $this->align;
    }

    /**
     * Get featured supported by the block
     *
     * @since 0.1.0
     * @return array
     */
    public function getSupports(): array
    {
        return $this->supports;
    }

    /**
     * Get the block registration data
     *
     * @since 0.1.0
     * @return array
     */
    public function getBlockData(): array
    {
        return [
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'category' => $this->getCategory(),
            'icon' => $this->getIcon(),
            'keywords' => $this->getKeywords(),
            'post_types' => $this->getPostTypes(),
            'mode' => $this->getMode(),
            'align' => $this->getAlignment(),
            'supports' => $this->getSupports(),
        ];
    }


    public function init(): void
    {
        $block_data = $this->getBlockData();
        $block_data['render_callback'] = [$this, 'renderBlockCallback'];
        $fields = $this->getFields();

        acf_register_block($block_data);

        if (! empty($fields)) {
            acf_add_local_field_group($fields);
        }
    }
}
