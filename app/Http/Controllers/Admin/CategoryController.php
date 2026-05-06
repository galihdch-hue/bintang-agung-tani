<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->ordered()
            ->paginate(10);

        return view('admin.kategori', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.tambah-kategori');
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect('/admin/kategori')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.edit-kategori', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect('/admin/kategori')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category has products (including soft deleted ones to respect DB constraints)
        if ($category->products()->withTrashed()->count() > 0) {
            return redirect('/admin/kategori')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk (termasuk produk yang sudah dihapus)');
        }

        $category->delete();

        return redirect('/admin/kategori')
            ->with('success', 'Kategori berhasil dihapus');
    }

    /**
     * Toggle the active status of the category.
     */
    public function toggleActive(Category $category)
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()
            ->with('success', "Kategori berhasil {$status}");
    }
}
