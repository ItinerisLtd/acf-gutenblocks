<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

abstract class AbstractBlock extends Block implements InitializableInterface
{
    public function fileExtension(): string
    {
        return '.php';
    }

    public function isValid(): bool
    {
        return true;
    }

    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acf_gutenblocks/render_block_frontend_path',
            "{$this->dir}/views/frontend{$this->fileExtension()}",
            $this,
        );

        if (file_exists($frontend)) {
            $path = $frontend;
        } else {
            $path = locate_template($frontend);
        }

        if (empty($path)) {
            return;
        }

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = Util::sanitizeHtmlClasses([
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        $controller = $this;

        // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
        extract($this->with());

        ob_start();

        // TODO: Check for remote file inclusion (WP VIP).
        // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.IncludingFile
        include apply_filters('acf_gutenblocks/render_block_html', $path, $controller);

        $html = ob_get_clean();

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo apply_filters('acf_gutenblocks/render_block_html_output', $html, $controller);
    }
}
