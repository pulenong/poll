<?php

declare(strict_types=1);

namespace Application\Model\Entity;

use Application\Model\SlugTrait;

class PollEntity
{
    use SlugTrait;

    private int|string $poll_id;
    private int|string $user_id;
    private string $title;
    private ?string $question;
    private int|string $status;
    private int|string $category_id;
    private int|string $total_votes;
    private int|string $comment_count;
    private int|string $view_count;
    private ?string $timeout;
    private ?string $created;
    #users table column
    private ?string $username;
    private ?string $picture;
    #poll_categories table column
    private ?string $category;

    public function getPollId():int|string
    {
        return $this->poll_id;
    }

    public function setPollId(int|string $poll_id): PollEntity
    {
        $this->poll_id = $poll_id;
        return $this;
    }

    public function getUserId(): int|string
    {
        return $this->user_id;
    }

    public function setUserId(int|string $user_id): PollEntity
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): PollEntity
    {
        $this->title = $title;
        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): PollEntity
    {
        $this->question = $question;
        return $this;
    }

    public function getStatus(): int|string
    {
        return $this->status == 1 ? 'Active' : 'Closed';
    }

    public function setStatus(int|string $status): PollEntity
    {
        $this->status = $status;
        return $this;
    }

    public function getCategoryId(): int|string
    {
        return $this->category_id;
    }

    public function setCategoryId(int|string $category_id): PollEntity
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getTotalVotes(): int|string
    {
        return $this->total_votes;
    }

    public function setTotalVotes(int|string $total_votes): PollEntity
    {
        $this->total_votes = $total_votes;
        return $this;
    }

    public function getCommentCount(): int|string
    {
        return $this->comment_count;
    }

    public function setCommentCount(int|string $comment_count): PollEntity
    {
        $this->comment_count = $comment_count;
        return $this;
    }

    public function getViewCount(): int|string
    {
        return $this->view_count;
    }

    public function setViewCount(int|string $view_count): PollEntity
    {
        $this->view_count = $view_count;
        return $this;
    }

    public function getTimeout(): ?string
    {
        return $this->timeout;
    }

    public function setTimeout(?string $timeout): PollEntity
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): PollEntity
    {
        $this->created = $created;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): PollEntity
    {
        $this->username = $username;
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): PollEntity
    {
        $this->picture = $picture;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): PollEntity
    {
        $this->category = $category;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->createSlug($this->title);
    }
}
