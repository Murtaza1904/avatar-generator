<?php

declare(strict_types=1);

namespace murtaza1904\AvatarGenerator;

/**
 * Avatar Generator
 *
 * Generates user avatars as SVG or PNG images with customizable
 * colors, sizes, formats, filenames, and storage locations.
 */
final class Avatar
{
    /** @var string The display name used to generate initials */
    protected string $name;

    /** @var int Avatar size in pixels (width and height) */
    protected int $size;

    /** @var string Background color (hex) */
    protected string $bg;

    /** @var string Text color (hex) */
    protected string $color;

    /** @var int Font size for initials */
    protected int $fontSize;

    /** @var string|null Explicit filename (with extension) if set */
    protected ?string $filename = null;

    /** @var string Image format (png|svg) */
    protected string $format = 'png';

    /** @var int|null Border radius */
    protected ?int $radius = null;

    /** @var string|null Full path where the avatar was last saved */
    protected ?string $outputPath = null;

    /** @var array<string> Default color palette */
    protected array $palette = [
        '#1abc9c','#2ecc71','#3498db','#9b59b6','#34495e',
        '#16a085','#27ae60','#2980b9','#8e44ad','#2c3e50',
        '#f39c12','#d35400','#c0392b','#7f8c8d','#e67e22',
    ];

    /**
     * Initialize a new avatar from a name.
     */
    public function create(string $name): self
    {
        $this->name     = $name;
        $this->size     = config('avatar.width', 128);
        $this->bg       = $this->colorFromString($name);
        $this->color    = config('avatar.color', '#ffffff');
        $this->format   = config('avatar.format', 'png');
        $this->radius = config('avatar.radius');
        $this->filename = null;
        $this->fontSize = config('avatar.font_size')
                            ? (int)config('avatar.font_size')
                            : (int)round($this->size * 0.42);

        return $this;
    }

    /**
     * Set avatar size (px).
     */
    public function size(int $size): self
    {
        $this->size     = $size;
        $this->fontSize = (int)round($size * 0.42);
        return $this;
    }

    /**
     * Set text color.
     */
    public function color(string $hex): self
    {
        $this->color = $hex;
        return $this;
    }

    /**
     * Set background color.
     */
    public function background(string $hex): self
    {
        $this->bg = $hex;
        return $this;
    }

    /**
     * Set output format (png|svg).
     */
    public function format(string $format): self
    {
        $this->format = strtolower($format);
        return $this;
    }

    /**
     * Set font size explicitly (px).
     */
    public function fontSize(int $size): self
    {
        $this->fontSize = $size;
        return $this;
    }

    /**
     * Set border radius explicitly (px).
     */
    public function radius(int $radius): self
    {
        $this->radius = $radius;
        return $this;
    }

    /**
     * Manually set the filename (without extension).
     */
    public function filename(string $name): self
    {
        $this->filename = $name . '.' . $this->format;
        return $this;
    }

    /**
     * Save the avatar in the given directory.
     *
     * @param string $dir Directory path
     * @return string The saved filename (with extension)
     */
    public function path(string $dir): string
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $this->filename
            ?? $this->resolveFilename($this->name);

        $fullPath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($fullPath, $this->render());

        $this->outputPath = $fullPath;
        $this->filename   = $filename;

        return $this->filename;
    }

    /**
     * Save the avatar (uses config storage if path not provided).
     */
    public function save(?string $path = null): string
    {
        $dir = $path ? dirname($path) : config('avatar.storage');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $this->filename
            ?? $this->resolveFilename($this->name);

        $fullPath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($fullPath, $this->render());
        $this->outputPath = $fullPath;
        $this->filename   = $filename;

        return $filename;
    }

    /**
     * Generate filename based on pattern in config.
     */
    protected function resolveFilename(string $name): string
    {
        $pattern = config('avatar.filename_pattern', '{name}-{timestamp}.{ext}');
        return str_replace(
            ['{name}', '{timestamp}', '{ext}'],
            [strtolower(preg_replace('/\s+/', '-', $name)), time(), $this->format],
            $pattern
        );
    }

    /**
     * Get last saved filename.
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Render the avatar image to a string (binary for PNG, XML for SVG).
     */
    public function render(): string
    {
        return match ($this->format) {
            'png' => $this->renderPng(),
            'svg' => $this->renderSvg(),
            default => throw new \InvalidArgumentException("Unsupported format: {$this->format}"),
        };
    }

    /**
     * Render the avatar as SVG.
     */
    protected function renderSvg(): string
    {
        $initials = $this->initials($this->name);
        return $this->buildSvg($this->size, $this->bg, $this->color, $this->fontSize, $initials);
    }

    /**
     * Build the SVG string.
     */
    protected function buildSvg(int $size, string $bg, string $color, int $fontSize, string $text): string
    {
        $bg       = $this->escape($bg);
        $color    = $this->escape($color);
        $text     = $this->escape($text);
        $x        = $size / 2;
        $y        = $size / 2;

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="$size" height="$size" viewBox="0 0 $size $size" role="img" aria-label="$text">
                <rect width="100%" height="100%" rx="{$this->calcRadius($size)}" fill="$bg"/>
                <text x="$x" y="$y"
                      font-family="system-ui,-apple-system,'Segoe UI',Roboto,Ubuntu,'Helvetica Neue',Arial"
                      font-size="$fontSize"
                      fill="$color"
                      text-anchor="middle"
                      dominant-baseline="middle"
                      alignment-baseline="middle">$text</text>
            </svg>
        SVG;
    }

    /**
     * Calculate corner radius based on size.
     */
    protected function calcRadius(int $size): int
    {
        return $this->radius !== null
            ? $this->radius
            : (int)round($size * 0.12);
    }

    /**
     * Escape values for safe XML output.
     */
    protected function escape(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Render the avatar as PNG binary.
     */
    protected function renderPng(): string
    {
        $initials = $this->initials($this->name);
        $size     = $this->size;
        $fontSize = $this->fontSize;

        $image = imagecreatetruecolor($size, $size);

        [$r, $g, $b]   = sscanf($this->bg, "#%02x%02x%02x");
        $bgColor       = imagecolorallocate($image, $r, $g, $b);
        [$r2, $g2, $b2] = sscanf($this->color, "#%02x%02x%02x");
        $textColor     = imagecolorallocate($image, $r2, $g2, $b2);

        imagefilledrectangle($image, 0, 0, $size, $size, $bgColor);

        $fontFile = __DIR__.'/../fonts/arial.ttf';
        if (! file_exists($fontFile)) {
            throw new \RuntimeException('Font file not found: '.$fontFile);
        }

        $bbox       = imagettfbbox($fontSize, 0, $fontFile, $initials);
        $textWidth  = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x          = (int)(($size - $textWidth) / 2);
        $y          = (int)(($size + $textHeight) / 2);

        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $initials);

        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return $pngData;
    }

    /**
     * Generate initials from a name string.
     */
    protected function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (! $parts) {
            return '';
        }
        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1));
        }
        $first = mb_substr($parts[0], 0, 1);
        $last  = mb_substr(end($parts), 0, 1);
        return mb_strtoupper($first . $last);
    }

    /**
     * Pick a background color from the palette based on a hash of the name.
     */
    protected function colorFromString(string $str): string
    {
        $palette = config('avatar.palette', $this->palette);
        $hash    = md5($str);
        $num     = hexdec(substr($hash, 0, 6));
        $idx     = $num % count($palette);
        return $palette[$idx];
    }
}
