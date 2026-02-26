<?php

namespace App\Services;

use App\Models\Collezione;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CartaImageService
{
    const BASE_DIR = 'img';

    /**
     * Normalise a collection name into a filesystem-safe folder name.
     */
    public function folderName(Collezione|string $collezione): string
    {
        $name = $collezione instanceof Collezione ? $collezione->nome : $collezione;
        return Str::slug($name);
    }

    /**
     * Store an uploaded image inside img/{collection-slug}/.
     * Returns the relative path stored in the DB: "{slug}/{filename}"
     */
    public function store(UploadedFile $file, Collezione $collezione): string
    {
        $slug     = $this->folderName($collezione);
        $dir      = public_path(self::BASE_DIR . '/' . $slug);
        $filename = 'carta_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $filename);

        return $slug . '/' . $filename;
    }

    /**
     * Delete the physical file for a stored relative path.
     * Silently ignores missing files.
     */
    public function delete(?string $relativePath): void
    {
        if (!$relativePath) return;

        $abs = public_path(self::BASE_DIR . '/' . $relativePath);
        if (is_file($abs)) {
            unlink($abs);
        }
    }

    /**
     * Build the public asset URL from the relative path stored in the DB.
     */
    public function assetUrl(?string $relativePath): ?string
    {
        return $relativePath ? asset(self::BASE_DIR . '/' . $relativePath) : null;
    }
}
