<?php

declare(strict_types=1);

namespace Softspring\CmsBlogPlugin\Model;

interface AuthorInterface
{
    public function getDisplayName(): string;

    public function getAvatarUrl(): string;
}
