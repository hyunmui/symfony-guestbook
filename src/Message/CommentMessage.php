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
    private $context;

    public function __construct(int $id, array $context = [])
    {
        $this->id = $id;
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
}

/** End of CommentMessage.php */
