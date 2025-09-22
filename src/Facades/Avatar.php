<?php

declare(strict_types=1);

namespace murtaza1904\AvatarGenerator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Avatar
 *
 * Facade for the Avatar Generator.
 * Provides a static interface to the underlying Avatar class.
 *
 * Example usage:
 * ```php
 * Avatar::create('John Doe')
 *     ->size(128)
 *     ->color('#ffffff')
 *     ->background('#3498db')
 *     ->format('png')
 *     ->save(storage_path('app/public/avatars'));
 * ```
 *
 * @method static Avatar create(string $name)       Create a new avatar instance with the given name
 * @method static Avatar size(int $size)            Set the avatar size in pixels
 * @method static Avatar color(string $hex)         Set the text color (hex format)
 * @method static Avatar background(string $hex)    Set the background color (hex format)
 * @method static Avatar format(string $format)     Set the output format (png or svg)
 * @method static Avatar filename(string $name)     Set a custom filename for saving
 * @method static string path(string $dir)          Save avatar to a directory and return filename
 * @method static string save(?string $path = null) Save avatar to configured storage or custom path
 * @method static string render()                   Render the avatar and return as raw string (SVG/XML or PNG binary)
 * @method static string|null getFilename()         Get the resolved filename
 */
final class Avatar extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'avatar.generator';
    }
}
