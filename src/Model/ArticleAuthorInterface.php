<?php

namespace Softspring\CmsBlogPlugin\Model;

interface ArticleAuthorInterface
{
    public function getAuthor(): ?AuthorInterface;

    public function setAuthor(?AuthorInterface $author): void;
}
