<?php

namespace App;

use App\Entity\Comment;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Spam Checker with Akismet
 * 
 * @author Martin Seon
 * @package App
 */
class SpamChecker
{
    /**
     * 
     * @var HttpClientInterface
     */
    private $client;

    /**
     * 
     * @var string
     */
    private $endpoint;

    /**
     * Class constructor.
     */
    public function __construct(HttpClientInterface $client, string $akismetKey)
    {
        $this->client = $client;
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    /**
     * 
     * @author Martin Seon
     * @param Comment $comment 
     * @param array $context 
     * @return int 0: No Spam / 1: Possible spam / 2: Blatant Spam
     */
    public function getSpamScore(Comment $comment, array $context): int
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'body' => array_merge($context, [
                'blog' => 'https://localhost:8000',
                'comment_type' => 'comment',
                'comment_author' => $comment->getAuthor(),
                'comment_author_email' => $comment->getEmail(),
                'comment_content' => $comment->getText(),
                'comment_date_gmt' => $comment->getCreatedAt()->format('c'),
                'blog_lang' => 'en',
                'blog_charset' => 'UTF-8',
                'is_test' => true,
            ]),
        ]);

        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new \RuntimeException(sprintf(
                'Unable to check for spam: %s (%s).',
                $content,
                $headers['x-akismet-debug-help'][0]
            ));
        }

        return 'true' === $content ? 1 : 0;
    }
}

/** End of SpamChecker.php */
