<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

interface InitializableInterface
{
    public function with(array $template_data): array;

    public function fileExtension(): string;

    public function isValid(): bool;

    public function renderBlockCallback(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void;

    public function init(): void;
}
