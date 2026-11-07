<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    /**
     * Tampilkan daftar seluruh banner & promo
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = Banner::query()->orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($type && in_array($type, ['slider', 'side'])) {
            $query->where('banner_type', $type);
        }

        $banners = $query->paginate(15)->withQueryString();

        $totalSlider = Banner::where('banner_type', 'slider')->count();
        $totalSide = Banner::where('banner_type', 'side')->count();
        $totalActive = Banner::where('is_active', true)->count();

        return view('admin.banners.index', compact('banners', 'type', 'totalSlider', 'totalSide', 'totalActive'));
    }

    /**
     * Tampilkan form pembuatan banner baru
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Simpan banner baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'discount_tag' => 'nullable|string|max:50',
            'discount_value' => 'nullable|string|max:50',
            'discount_subtext' => 'nullable|string|max:100',
            'button_text' => 'required|string|max:50',
            'button_url' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'preset_image' => 'nullable|string|max:255',
            'banner_type' => 'required|in:slider,side',
            'gradient_theme' => 'required|in:crimson,slate,emerald,amber,blue',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $imagePath = null;

        // Tangani unggah file gambar kustom
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/banners');
            
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $imagePath = 'images/banners/' . $filename;
        } elseif (!empty($validated['preset_image'])) {
            $imagePath = $validated['preset_image'];
        }

        $banner = Banner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'discount_tag' => $validated['discount_tag'] ?? null,
            'discount_value' => $validated['discount_value'] ?? null,
            'discount_subtext' => $validated['discount_subtext'] ?? null,
            'button_text' => $validated['button_text'],
            'button_url' => $validated['button_url'],
            'image' => $imagePath,
            'banner_type' => $validated['banner_type'],
            'gradient_theme' => $validated['gradient_theme'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Bersihkan cache beranda
        Cache::forget('active_slider_banners');
        Cache::forget('active_side_banners');

        // Catat AuditLog
        AuditLog::record(
            action: 'banner.tambah',
            targetType: 'Banner',
            targetId: $banner->id,
            oldValues: null,
            newValues: $banner->toArray()
        );

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner promo '{$banner->title}' berhasil ditambahkan ke katalog promosi.");
    }

    /**
     * Tampilkan form ubah banner
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Perbarui data banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'discount_tag' => 'nullable|string|max:50',
            'discount_value' => 'nullable|string|max:50',
            'discount_subtext' => 'nullable|string|max:100',
            'button_text' => 'required|string|max:50',
            'button_url' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'preset_image' => 'nullable|string|max:255',
            'banner_type' => 'required|in:slider,side',
            'gradient_theme' => 'required|in:crimson,slate,emerald,amber,blue',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $oldValues = $banner->toArray();
        $imagePath = $banner->image;

        // Tangani unggah file gambar baru jika ada
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/banners');

            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $imagePath = 'images/banners/' . $filename;
        } elseif ($request->filled('preset_image')) {
            $imagePath = $validated['preset_image'];
        }

        $banner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'discount_tag' => $validated['discount_tag'] ?? null,
            'discount_value' => $validated['discount_value'] ?? null,
            'discount_subtext' => $validated['discount_subtext'] ?? null,
            'button_text' => $validated['button_text'],
            'button_url' => $validated['button_url'],
            'image' => $imagePath,
            'banner_type' => $validated['banner_type'],
            'gradient_theme' => $validated['gradient_theme'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Bersihkan cache beranda
        Cache::forget('active_slider_banners');
        Cache::forget('active_side_banners');

        // Catat AuditLog
        AuditLog::record(
            action: 'banner.ubah',
            targetType: 'Banner',
            targetId: $banner->id,
            oldValues: $oldValues,
            newValues: $banner->fresh()->toArray()
        );

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner promo '{$banner->title}' berhasil diperbarui.");
    }

    /**
     * Hapus banner
     */
    public function destroy(Banner $banner)
    {
        $oldValues = $banner->toArray();
        $title = $banner->title;

        // Hapus file gambar jika berada di folder kustom banners dan bukan preset
        if ($banner->image && str_starts_with($banner->image, 'images/banners/banner_') && file_exists(public_path($banner->image))) {
            @unlink(public_path($banner->image));
        }

        $banner->delete();

        // Bersihkan cache beranda
        Cache::forget('active_slider_banners');
        Cache::forget('active_side_banners');

        // Catat AuditLog
        AuditLog::record(
            action: 'banner.hapus',
            targetType: 'Banner',
            targetId: $oldValues['id'],
            oldValues: $oldValues,
            newValues: null
        );

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner promo '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif banner secara instan
     */
    public function toggleStatus(Banner $banner)
    {
        $oldStatus = $banner->is_active;
        $banner->is_active = !$oldStatus;
        $banner->save();

        // Bersihkan cache beranda
        Cache::forget('active_slider_banners');
        Cache::forget('active_side_banners');

        AuditLog::record(
            action: 'banner.toggle_status',
            targetType: 'Banner',
            targetId: $banner->id,
            oldValues: ['is_active' => $oldStatus],
            newValues: ['is_active' => $banner->is_active]
        );

        $statusText = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Status banner '{$banner->title}' berhasil {$statusText}.");
    }
}

