<?php declare(strict_types=1);

namespace GeekBrains\Blog;

/**
 * Class Comment
 * @package GeekBrains\Blog
 */
final class Comment
{

    /**
     * Comment constructor.
     * @param CommentId $commentId
     * @param UUID $authorUuid
     * @param string $text
     */
    public function __construct(
        private CommentId $commentId,
        private UUID $authorUuid,
        private string $text,
    ) {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->commentId->uuid();
    }

    /**
     * @return UUID
     */
    public function parentUuid(): UUID
    {
        return $this->commentId->parentUuid();
    }

    /**
     * @return UUID
     */
    public function authorUuid(): UUID
    {
        return $this->authorUuid;
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }
}
