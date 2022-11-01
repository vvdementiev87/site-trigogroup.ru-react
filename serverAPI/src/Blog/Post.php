<?php

namespace devavi\leveltwo\Blog;

use DateTimeImmutable;

class Post
{

    private UUID    $uuid;
    private User    $user;
    private string  $title;
    private string  $text;
    private string  $textShort;
    private string  $category;
    private DateTimeImmutable $date;

    public function __construct(UUID $uuid, User $user, string $title, string $text, string $textShort, string  $category, DateTimeImmutable $date)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;
        $this->textShort = $textShort;
        $this->category = $category;
        $this->date = $date;
    }

    public function __toString(): string
    {
        return "Юзер $this->user написал пост номер $this->uuid с заголовком: $this->title и текстом: $this->text" . PHP_EOL;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function textShort(): string
    {
        return $this->textShort;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }
}
