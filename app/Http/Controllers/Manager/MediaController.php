<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    const MAX_IMAGES = 5;

    // ─── Property Media ──────────────────────────────────────────────────────

    public function storePropertyMedia(Request $request, Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.required'   => 'Pilih minimal 1 gambar untuk diupload.',
            'images.*.image'    => 'File harus berupa gambar.',
            'images.*.mimes'    => 'Format yang didukung: JPG, JPEG, PNG, WebP.',
            'images.*.max'      => 'Ukuran maksimal per gambar adalah 5 MB.',
        ]);

        $existing = $property->media()->where('type', 'image')->count();
        $new      = count($request->file('images'));

        if ($existing + $new > self::MAX_IMAGES) {
            return back()->with('error', "Maksimal " . self::MAX_IMAGES . " gambar per properti. Saat ini sudah ada {$existing} gambar, hanya bisa menambah " . (self::MAX_IMAGES - $existing) . " lagi.");
        }

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('property-media', 'public');

            PropertyMedia::create([
                'property_id' => $property->id,
                'unit_id'     => null,
                'file_path'   => $path,
                'file_name'   => $file->getClientOriginalName(),
                'type'        => 'image',
                'is_primary'  => ($existing === 0 && $index === 0), // First ever image = primary
                'sort_order'  => $existing + $index,
            ]);
        }

        return back()->with('success', "{$new} gambar berhasil diupload.");
    }

    // ─── Unit Media ───────────────────────────────────────────────────────────

    public function storeUnitMedia(Request $request, Unit $unit)
    {
        if ($unit->property->manager_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.required'   => 'Pilih minimal 1 gambar untuk diupload.',
            'images.*.image'    => 'File harus berupa gambar.',
            'images.*.mimes'    => 'Format yang didukung: JPG, JPEG, PNG, WebP.',
            'images.*.max'      => 'Ukuran maksimal per gambar adalah 5 MB.',
        ]);

        $existing = $unit->media()->where('type', 'image')->count();
        $new      = count($request->file('images'));

        if ($existing + $new > self::MAX_IMAGES) {
            return back()->with('error', "Maksimal " . self::MAX_IMAGES . " gambar per unit. Saat ini sudah ada {$existing} gambar, hanya bisa menambah " . (self::MAX_IMAGES - $existing) . " lagi.");
        }

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('property-media', 'public');

            PropertyMedia::create([
                'property_id' => $unit->property_id,
                'unit_id'     => $unit->id,
                'file_path'   => $path,
                'file_name'   => $file->getClientOriginalName(),
                'type'        => 'image',
                'is_primary'  => ($existing === 0 && $index === 0),
                'sort_order'  => $existing + $index,
            ]);
        }

        return back()->with('success', "{$new} gambar unit berhasil diupload.");
    }

    // ─── Shared: Delete & Set Primary ────────────────────────────────────────

    public function destroy(PropertyMedia $media)
    {
        // Ownership check
        $managerId = $media->unit_id
            ? $media->unit->property->manager_id
            : $media->property->manager_id;

        if ($managerId !== Auth::id()) {
            abort(403);
        }

        $wasPrimary = $media->is_primary;
        $propertyId = $media->property_id;
        $unitId     = $media->unit_id;

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        // If deleted was primary, promote the next image as primary
        if ($wasPrimary) {
            $query = PropertyMedia::where('property_id', $propertyId)
                                  ->where('type', 'image');
            if ($unitId) {
                $query->where('unit_id', $unitId);
            } else {
                $query->whereNull('unit_id');
            }
            $next = $query->orderBy('sort_order')->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    public function setPrimary(PropertyMedia $media)
    {
        $managerId = $media->unit_id
            ? $media->unit->property->manager_id
            : $media->property->manager_id;

        if ($managerId !== Auth::id()) {
            abort(403);
        }

        // Unset all primaries in same scope
        $query = PropertyMedia::where('property_id', $media->property_id);
        if ($media->unit_id) {
            $query->where('unit_id', $media->unit_id);
        } else {
            $query->whereNull('unit_id');
        }
        $query->update(['is_primary' => false]);

        $media->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama berhasil diubah.');
    }
}
