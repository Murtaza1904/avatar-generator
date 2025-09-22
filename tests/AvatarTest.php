<?php

declare(strict_types=1);

namespace murtaza1904\AvatarGenerator\Tests;

use murtaza1904\AvatarGenerator\Facades\Avatar;

final class AvatarTest extends TestCase
{
    public function test_it_generates_svg_string(): void
    {
        $svg = Avatar::create('Muhammad Murtaza')
            ->format('svg')
            ->render();

        $this->assertStringStartsWith('<svg', ltrim($svg));
        $this->assertStringContainsString('MM', $svg);
    }

    public function test_it_generates_png_binary(): void
    {
        $png = Avatar::create('John Doe')
            ->format('png')
            ->render();

        // PNG files always start with these bytes
        $this->assertStringStartsWith("\x89PNG", $png);
    }

    public function test_it_saves_avatar_file(): void
    {
        $path = sys_get_temp_dir(); // use system temp directory

        $filename = Avatar::create('Jane Smith')
            ->format('png')
            ->filename('jane-smith')
            ->path($path);

        $fullPath = $path . DIRECTORY_SEPARATOR . $filename;

        $this->assertFileExists($fullPath);
        $this->assertStringEndsWith('.png', $filename);

        // cleanup
        @unlink($fullPath);
    }
}
