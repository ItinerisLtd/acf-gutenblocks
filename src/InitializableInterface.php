<?php

declare(strict_types=1);

namespace Itineris\AcfGutenblocks;

interface InitializableInterface
{
    public function fileExtension(): string;

    public function isValid(): bool;

    public function renderBlockCallback(array $block): void;

    public function init(): void;
}
