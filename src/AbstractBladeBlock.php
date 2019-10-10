<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

use function App\template;

abstract class AbstractBladeBlock extends Block implements InitializableInterface
{
    public function fileExtension(): string
    {
        return '.blade.php';
    }

    public function getBladeEngineCallable(): string
    {
        return apply_filters(
            'acf_gutenblocks/blade_engine_callable',
            '\App\template',
            "{$this->dir}/views/frontend{$this->fileExtension()}",
            $this
        );
    }

    public function isValid(): bool
    {
        return function_exists($this->getBladeEngineCallable());
    }

    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acf_gutenblocks/render_block_frontend_path',
            "{$this->dir}/views/frontend{$this->fileExtension()}",
            $this
        );

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = Util::sanitizeHtmlClasses([
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo call_user_func($this->getBladeEngineCallable(), $frontend, [
            'block' => $block,
            'controller' => $this,
        ]);
    }
}
