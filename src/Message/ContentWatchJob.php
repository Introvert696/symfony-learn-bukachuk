<?php

namespace App\Message;

class ContentWatchJob
{
    public function __construct(
        private string $content,
    )
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}