<?php

namespace Softspring\CmsBlogPlugin\Model;

use Softspring\UserBundle\Model\UserInterface;

trait ArticleAuthorTrait
{
    protected ?UserInterface $author;

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): void
    {
        $this->author = $author;
    }
}
