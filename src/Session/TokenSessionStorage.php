<?php declare(strict_types=1);


namespace GeekBrains\Blog\Session;


use Exception;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * Class TokenSessionStorage
 * @package GeekBrains\Blog\Session
 */
final class TokenSessionStorage implements SessionStorageInterface
{
    /**
     * @var string
     */
    private string $id = '';

    /**
     * @var string
     */
    private string $name = 'TOKENSESSID';

    /**
     * @var bool
     */
    private bool $started = false;

    /**
     * @var bool
     */
    private bool $closed = false;

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var MetadataBag
     */
    private MetadataBag $metadataBag;

    /**
     * @var SessionBagInterface[]
     */
    private array $bags = [];

    /**
     * TokenSessionStorage constructor.
     */
    public function __construct() {
        $this->metadataBag = new MetadataBag();
    }

    /**
     * @param bool $destroy
     * @param int|null $lifetime
     * @return bool
     * @throws Exception
     */
    public function regenerate(bool $destroy = false, int $lifetime = null): bool
    {
        if (!$this->started) {
            $this->start();
        }

        $this->metadataBag->stampNew($lifetime);
        $this->id = $this->generateId();

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (empty($this->id)) {
            $this->id = $this->generateId();
        }

        $this->loadSession();

        return true;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function generateId(): string
    {
        return hash('sha256', bin2hex(random_bytes(80)));
    }

    /**
     *
     */
    private function loadSession(): void
    {
        $bags = array_merge($this->bags, [$this->metadataBag]);

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $this->data[$key] = $this->data[$key] ?? [];
            $bag->initialize($this->data[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        if ($this->started) {
            throw new LogicException('Cannot set session ID after the session has started.');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     *
     */
    public function save(): void
    {
        if (!$this->started || $this->closed) {
            throw new RuntimeException('Trying to save a session that was not started yet or was already closed.');
        }
        // nothing to do since we don't persist the session data
        $this->closed = false;
        $this->started = false;
    }

    /**
     *
     */
    public function clear(): void
    {
        // clear out the bags
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // clear out the session
        $this->data = [];

        // reconnect the bags to the session
        $this->loadSession();
    }

    /**
     * @param SessionBagInterface $bag
     */
    public function registerBag(SessionBagInterface $bag): void
    {
        $this->bags[$bag->getName()] = $bag;
    }

    /**
     * @param string $name
     * @return SessionBagInterface
     * @throws Exception
     */
    public function getBag(string $name): SessionBagInterface
    {
        if (!isset($this->bags[$name])) {
            throw new InvalidArgumentException(sprintf('The SessionBagInterface "%s" is not registered.', $name));
        }

        if (!$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @return MetadataBag
     */
    public function getMetadataBag(): MetadataBag
    {
        return $this->metadataBag;
    }
}
