<?php

namespace Softspring\CmsBlogPlugin\Model;

interface AuthorInterface
{
    public function getDisplayName(): string;

    public function getAvatarUrl(): string;
}