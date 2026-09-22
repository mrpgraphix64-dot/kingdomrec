<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GalleryItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display the Admin Gallery Dashboard.
     */
    public function index(Request $request)
    {
        // Summary counts (status counts removed)
        $stats = [
            'total_items' => GalleryItem::count(),
            'images_count' => GalleryItem::where('type', 'image')->count(),
            'videos_count' => GalleryItem::where('type', 'video')->count(),
        ];

        // Query builder for gallery items
        $query = GalleryItem::query();

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Fetch items ordered by sort_order
        $items = $query->orderBy('sort_order', 'asc')
                       ->orderBy('created_at', 'desc')
                       ->get();

        $maxUploadSizeKb = 102400; // Hardcoded to 100MB

        return view('admin.gallery.index', compact('stats', 'items', 'maxUploadSizeKb'));
    }

    /**
     * Upload and Save Gallery Items (Images and Videos).
     */
    public function storeItems(Request $request)
    {
        $maxSizeKb = 102400; // Hardcoded to 100MB

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,jpg,png,webp,mp4,webm|max:' . $maxSizeKb,
        ]);

        $maxOrder = GalleryItem::max('sort_order') ?? -1;

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('gallery/thumbnails');

        $uploadedCount = 0;

        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanTitle = ucwords(str_replace(['-', '_'], ' ', $originalName));
            
            // Detect Type (image vs video)
            $mime = $file->getMimeType();
            $type = 'image';
            if (str_starts_with($mime, 'video/')) {
                $type = 'video';
            }

            // Save original file
            $filePath = $file->store('gallery', 'public');
            $thumbnailPath = null;

            if ($type === 'image') {
                // Generate compressed thumbnail for images
                $srcFullPath = storage_path('app/public/' . $filePath);
                $thumbRelativeFolder = 'gallery/thumbnails/';
                $thumbFileName = basename($filePath);
                $dstFullPath = storage_path('app/public/' . $thumbRelativeFolder . $thumbFileName);

                if ($this->generateThumbnail($srcFullPath, $dstFullPath, 450)) {
                    $thumbnailPath = $thumbRelativeFolder . $thumbFileName;
                }
            }

            GalleryItem::create([
                'type' => $type,
                'file_path' => $filePath,
                'thumbnail_path' => $thumbnailPath,
                'title' => $cleanTitle,
                'description' => null,
                'alt_text' => $cleanTitle,
                'status' => 'published', // defaults to published
                'sort_order' => ++$maxOrder,
            ]);

            $uploadedCount++;
        }

        return redirect()->back()->with('success', "Successfully uploaded {$uploadedCount} item(s).");
    }

    /**
     * Update Gallery Item Metadata and Custom Thumbnail.
     */
    public function updateItem(Request $request, $id)
    {
        $item = GalleryItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', // optional thumbnail update
        ]);

        $item->update([
            'title' => $request->title,
            'description' => $request->description,
            'alt_text' => $request->alt_text ?: $request->title,
            'sort_order' => $request->sort_order,
        ]);

        // Handle custom thumbnail uploading
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($item->thumbnail_path) {
                Storage::disk('public')->delete($item->thumbnail_path);
            }

            $thumbFile = $request->file('thumbnail');
            $thumbPath = $thumbFile->store('gallery/thumbnails', 'public');
            
            // Compress custom thumbnail
            $srcFullPath = storage_path('app/public/' . $thumbPath);
            $dstFullPath = storage_path('app/public/' . $thumbPath);
            $this->generateThumbnail($srcFullPath, $dstFullPath, 450);

            $item->update(['thumbnail_path' => $thumbPath]);
        }

        return redirect()->back()->with('success', 'Gallery item updated successfully.');
    }

    /**
     * Delete a single gallery item.
     */
    public function destroyItem($id)
    {
        $item = GalleryItem::findOrFail($id);
        $item->delete(); // Model deleting event handles physical cleanup

        return redirect()->back()->with('success', 'Gallery item deleted successfully.');
    }

    /**
     * Reorder items (AJAX).
     */
    public function reorderItems(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:gallery_items,id',
        ]);

        foreach ($request->ids as $index => $id) {
            GalleryItem::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Gallery order updated.']);
    }

    /**
     * Bulk Action handler.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:gallery_items,id',
            'action' => 'required|string|in:delete', // delete is the only bulk action now
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'delete') {
            $items = GalleryItem::whereIn('id', $ids)->get();
            foreach ($items as $item) {
                $item->delete(); // cascades cleanups
            }
            $message = 'Selected items deleted successfully.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * GD library image resizing logic.
     */
    private function generateThumbnail(string $src, string $dst, int $targetWidth): bool
    {
        if (!file_exists($src)) {
            return false;
        }

        list($width, $height, $type) = getimagesize($src);
        if ($width <= 0 || $height <= 0) {
            return false;
        }

        // Only scale down, don't scale up
        if ($width <= $targetWidth) {
            if ($src !== $dst) {
                return copy($src, $dst);
            }
            return true;
        }

        $targetHeight = (int) floor($height * ($targetWidth / $width));

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = imagecreatefromjpeg($src);
                break;
            case IMAGETYPE_PNG:
                $srcImg = imagecreatefrompng($src);
                break;
            case IMAGETYPE_WEBP:
                $srcImg = @imagecreatefromwebp($src);
                break;
            default:
                return false;
        }

        if (!$srcImg) {
            return false;
        }

        $dstImg = imagecreatetruecolor($targetWidth, $targetHeight);

        // Keep transparency for PNG and WEBP
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagecolortransparent($dstImg, imagecolorallocatealpha($dstImg, 0, 0, 0, 127));
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dstImg, $dst, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($dstImg, $dst, 6);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($dstImg, $dst, 85);
                break;
        }

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        return true;
    }
}
