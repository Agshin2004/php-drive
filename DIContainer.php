<?php

use App\Exceptions\Container\NotFoundException;
use Psr\Container\ContainerInterface;

// custom DI Container, NOT USED IN PROJECT JUST FOR FUN

class DIContainer implements ContainerInterface
{
    private array $entries = [];

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Class {$id} has no binding");
        }

        $entry = $this->entries[$id]; // $entry is callable that the $id is bound to

        return $entry($this); // passing container instance to the callable
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable $concrete): string
    {
        // $concrete is gonna be the implementation that $id is gonna be bound to
        $this->entries[$id] = $concrete;
    }
}
