<?php declare(strict_types=1);

namespace GeekBrains\Blog;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Exceptions\RuntimeException;

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
     * @throws InvalidArgumentException
     */
    private function __construct(
        private UUID $uuid,
        private UUID $parentUUID,
        string $parent
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
        try {
            return new self($uuid, $postUUID, self::POST);
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param UUID $commentUUID
     * @param UUID $uuid
     * @return CommentId
     */
    public static function forComment(UUID $commentUUID, UUID $uuid): self
    {
        try {
            return new self($uuid, $commentUUID, self::COMMENT);
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
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
