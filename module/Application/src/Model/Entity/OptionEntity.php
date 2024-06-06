<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class OptionEntity
{
    private int|string $option_id;
    private int|string $poll_id;
    private ?string $option;
    private int|string $vote_tally;
    #poll_votes table columns
    private int|string $user_id;
    private ?string $created;

    public function getOptionId(): int|string
    {
        return $this->option_id;
    }

    public function setOptionId(int|string $option_id): OptionEntity
    {
        $this->option_id = $option_id;
        return $this;
    }

    public function getPollId(): int|string
    {
        return $this->poll_id;
    }

    public function setPollId(int|string $poll_id): OptionEntity
    {
        $this->poll_id = $poll_id;
        return $this;
    }

    public function getOption(): ?string
    {
        return $this->option;
    }

    public function setOption(?string $option): OptionEntity
    {
        $this->option = $option;
        return $this;
    }

    public function getVoteTally(): int|string
    {
        return $this->vote_tally;
    }

    public function setVoteTally(int|string $vote_tally): OptionEntity
    {
        $this->vote_tally = $vote_tally;
        return $this;
    }

    public function getUserId(): int|string
    {
        return $this->user_id;
    }

    public function setUserId(int|string $user_id): OptionEntity
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): OptionEntity
    {
        $this->created = $created;
        return $this;
    }
}
