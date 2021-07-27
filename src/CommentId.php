<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;

/**
 * Class CommentId
 * @package GeekBrains\Blog
 */
final class CommentId
{

    private const COMMENT = 'comment';

    private const POST = 'post';

    /**
     * CommentId constructor.
     * @param UUID $uuid
     * @param UUID $parentUUID
     * @param string $parent
     */
    private function __construct(
        private UUID $uuid,
        private UUID $parentUUID,
        private string $parent
    ) {
        if (!in_array($parent, [self::COMMENT, self::POST])) {
            throw new InvalidArgumentException("Cannot create comment id for $parent");
        }
    }

    /**
     * @param UUID $postUUID
     * @param UUID $uuid
     * @return CommentId
     */
    public static function forPost(UUID $postUUID, UUID $uuid): self
    {
        return new self($uuid, $postUUID, self::POST);
    }

    /**
     * @param UUID $commentUUID
     * @param UUID $uuid
     * @return CommentId
     */
    public static function forComment(UUID $commentUUID, UUID $uuid): self
    {
        return new self($uuid, $commentUUID, self::COMMENT);
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function parentUuid(): UUID
    {
        return $this->parentUUID;
    }

}
