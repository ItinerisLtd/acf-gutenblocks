<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

final class Plugin
{
    /**
     * Class names of modules, settings and helpers.
     *
     * @var InitializableInterface[]
     */
    private $initializables = [];

    public function add(string ...$initializables): self
    {
        $this->initializables = array_merge($this->initializables, $initializables);
        return $this;
    }

    public function remove(string ...$initializables): self
    {
        $this->initializables = array_diff($this->initializables, $initializables);
        return $this;
    }

    public function getInitializables(): array
    {
        return apply_filters(
            'acf_gutenblocks/get_initializables',
            $this->initializables
        );
    }

    public function init(): void
    {
        foreach ($this->getInitializables() as $initializable) {
            $instance = new $initializable();

            if (! $instance->isValid() || ! $instance->isEnabled()) {
                unset($instance);
                continue 1;
            }

            $instance->init();
        }
    }
}
