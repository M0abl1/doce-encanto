<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('cakes')->orderBy('position')->orderBy('name')->get(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create([...$request->validated(), 'position' => $request->integer('position')]);

        return redirect()->route('admin.categories.index')->with('status', 'Categoria criada.');
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([...$request->validated(), 'position' => $request->integer('position')]);

        return redirect()->route('admin.categories.index')->with('status', 'Categoria atualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->cakes()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Não é possível excluir uma categoria que ainda tem bolos. Mova ou exclua os bolos antes.');
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Categoria excluída.');
    }
}
