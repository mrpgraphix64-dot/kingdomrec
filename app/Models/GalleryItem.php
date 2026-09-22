<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    protected $fillable = [
        'type',
        'file_path',
        'thumbnail_path',
        'title',
        'description',
        'alt_text',
        'status',
        'sort_order'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url', 'thumbnail_url'];

    protected static function boot()
    {
        parent::boot();

        // Delete physical files from disk when model is deleted
        static::deleting(function ($item) {
            if ($item->file_path) {
                Storage::disk('public')->delete($item->file_path);
            }
            if ($item->thumbnail_path) {
                Storage::disk('public')->delete($item->thumbnail_path);
            }
        });
    }

    /**
     * Check if the item is a video.
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Check if the item is an image.
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Get the full URL of the uploaded image/video.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '';
        }
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }
        $path = ltrim($this->file_path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        } elseif (str_starts_with($path, 'media/')) {
            $path = substr($path, 6);
        }
        return asset('media/' . $path);
    }

    /**
     * Get the URL of the thumbnail (falls back to original url).
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->thumbnail_path)) {
            if (str_starts_with($this->thumbnail_path, 'http://') || str_starts_with($this->thumbnail_path, 'https://')) {
                return $this->thumbnail_path;
            }
            $path = ltrim($this->thumbnail_path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            } elseif (str_starts_with($path, 'media/')) {
                $path = substr($path, 6);
            }
            return asset('media/' . $path);
        }
        return $this->url;
    }
}
