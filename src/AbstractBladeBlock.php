<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

abstract class AbstractBladeBlock extends Block implements InitializableInterface
{
    public function fileExtension(): string
    {
        return '.blade.php';
    }

    public function getBladeEngineCallable(): string
    {
        return (string) apply_filters(
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
        $template_data = array_merge(
            [
                'block' => $block,
                'controller' => $this,
            ],
            $this->with()
        );

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->getBladeEngineCallable()($frontend, $template_data);
    }
}
