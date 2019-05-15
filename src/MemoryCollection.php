<?php

namespace Live\Collection;

/**
 * Memory collection
 *
 * @package Live\Collection
 */
class MemoryCollection implements CollectionInterface
{
    /**
     * Default TTL for expiring the item in seconds.
     */
    const TTL = 1;

    /**
     * Collection data
     *
     * @var array
     */
    protected $data;

    /**
     * Collection metadata.
     *
     * @var array $metadata
     */
    protected $metadata;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = [];
        $this->metadata = [];
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $index, $defaultValue = null)
    {

        if (!$this->has($index) ||
            $this->shouldExpire($this->metadata[$index]['ttl'])) {

            return $defaultValue;
        }

        return $this->data[$index];
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $index, $value, $ttl = 0)
    {
        $ttl = (int)$ttl === 0 ? static::TTL : $ttl;

        $this->data[$index] = $value;
        $this->metadata[$index]['ttl'] = time() + (int)$ttl;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $index)
    {
        return array_key_exists($index, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->data = [];
    }

    /**
     * Check if the item timestamp is expired.
     *
     * @param int $timestamp
     * @return bool
     */
    public function shouldExpire(int $timestamp)
    {
        return $timestamp < time();
    }
}
