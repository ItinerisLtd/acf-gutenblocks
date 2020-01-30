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

    public function renderBlockCallback(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        $frontend = apply_filters(
            'acf_gutenblocks/render_block_frontend_path',
            "{$this->dir}/views/frontend{$this->fileExtension()}",
            $this
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

        $template_data = $this->getTemplateData($block, $is_preview, $post_id);

        extract($template_data);

        ob_start();

        // TODO: Check for remote file inclusion (WP VIP).
        // phpcs:disable WordPressVIPMinimum.Files.IncludingFile.IncludingFile
        include apply_filters('acf_gutenblocks/render_block_html', $path, $this);
        // phpcs:enable

        $html = ob_get_clean();

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo apply_filters('acf_gutenblocks/render_block_html_output', $html, $this);
        // phpcs:enable
    }
}
