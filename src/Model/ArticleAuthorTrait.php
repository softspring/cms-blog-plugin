<?php

namespace Softspring\CmsBlogPlugin\Model;

trait ArticleAuthorTrait
{
    protected ?AuthorInterface $author;

    public function getAuthor(): ?AuthorInterface
    {
        return $this->author;
    }

    public function setAuthor(?AuthorInterface $author): void
    {
        $this->author = $author;
    }
}
