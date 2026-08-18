<?php namespace Qdev\LaravelMinify\Contracts;

use Qdev\LaravelMinify\Exceptions\CannotSaveFileException;

interface MinifyInterface {
    /**
     * @throws CannotSaveFileException
     */
    public function minify(): string;

    public function tag(string $file, array $attributes): string;
}
