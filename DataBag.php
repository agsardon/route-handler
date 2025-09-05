<?php

namespace Models;

defined('ROOT') or die('Direct script access denied!');

/**
 * Class DataBag
 *
 * A simple Data Transfer Object (DTO) for storing and retrieving key-value pairs.
 * Useful for passing structured data between different application layers
 * without exposing internal structures.
 *
 * @package Models
 */
class DataBag
{
    /**
     * Internal storage for data.
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Set a value by key.
     *
     * @param string $key   The key to associate the value with.
     * @param mixed  $value The value to store.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Retrieve a value by key.
     *
     * @param string $key The key to look up.
     * @return mixed The stored value, or an empty string if not found.
     */
    public function get(string $key): mixed
    {
        return $this->data[$key] ?? '';
    }

    /**
     * Retrieve all stored data.
     *
     * @return array|object|null The stored data as an array, object, or null if empty.
     */
    public function getAll(): array|object|null
    {
        return $this->data ?? [];
    }
}

