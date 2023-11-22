<?php

namespace Softspring\CmsBlogPlugin\Model;

use Softspring\UserBundle\Model\UserInterface;

interface ArticleAuthorInterface
{
    public function getAuthor(): ?UserInterface;

    public function setAuthor(?UserInterface $author): void;
}
