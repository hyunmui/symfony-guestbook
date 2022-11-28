<?php

namespace App\Message;

/**
 * Comment Message Class
 * 
 * @author Martin Seon
 * @package App\Message
 */
class CommentMessage
{
    private $id;
    private $reviewUrl;
    private $context;

    public function __construct(int $id, string $reviewUrl, array $context = [])
    {
        $this->id = $id;
        $this->reviewUrl = $reviewUrl;
        $this->context = $context;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of content
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get the value of reviewUrl
     */
    public function getReviewUrl()
    {
        return $this->reviewUrl;
    }
}

/** End of CommentMessage.php */
