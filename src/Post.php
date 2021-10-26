<?php

namespace GeekBrains\Blog;

class Post
{
    public function __construct(
        private UUID $uuid,
        private UUID $authorUuid,
        private string $title,
        private string $text,
    ) {
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function authorUuid(): UUID
    {
        return $this->authorUuid;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return $this->title . ' >>> ' . $this->text;
    }
}
