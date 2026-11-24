<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CakeRequest;
use App\Models\Cake;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CakeController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim($request->text('busca')), 0, 60);

        return view('admin.cakes.index', [
            'cakes' => Cake::with('category')
                ->when($search !== '', fn ($q) => $q->search($search, ['name']))
                ->orderBy('name')->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.cakes.form', [
            'cake' => new Cake(['color' => '#C2416B', 'is_active' => true]),
            'categories' => Category::orderBy('position')->get(),
        ]);
    }

    public function store(CakeRequest $request): RedirectResponse
    {
        $cake = new Cake($request->cakeData());
        $newPath = $this->storeImage($request);
        $cake->image_path = $newPath;

        try {
            $cake->save();
        } catch (\Throwable $e) {
            $this->deleteImage($newPath); // não deixa arquivo órfão se o banco falhar
            throw $e;
        }

        return redirect()->route('admin.cakes.index')->with('status', "Item \"{$cake->name}\" cadastrado.");
    }

    public function edit(Cake $cake): View
    {
        return view('admin.cakes.form', [
            'cake' => $cake->load('category'),
            'categories' => Category::orderBy('position')->get(),
        ]);
    }

    public function update(CakeRequest $request, Cake $cake): RedirectResponse
    {
        $cake->fill($request->cakeData());
        $oldPath = $cake->image_path;
        $newPath = $this->storeImage($request);

        if ($newPath || $request->boolean('remove_image')) {
            $cake->image_path = $newPath;
        }

        try {
            $cake->save();
        } catch (\Throwable $e) {
            $this->deleteImage($newPath);
            throw $e;
        }

        // A foto antiga só é apagada depois que o banco já aponta para a nova.
        if ($oldPath && $oldPath !== $cake->image_path) {
            $this->deleteImage($oldPath);
        }

        return redirect()->route('admin.cakes.index')->with('status', "Item \"{$cake->name}\" atualizado.");
    }

    public function destroy(Cake $cake): RedirectResponse
    {
        $path = $cake->image_path;
        $cake->delete();
        $this->deleteImage($path);

        return redirect()->route('admin.cakes.index')->with('status', "Item \"{$cake->name}\" excluído.");
    }

    private function storeImage(CakeRequest $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }
        $path = $request->file('image')->store('bolos', 'public');
        if ($path === false) {
            throw ValidationException::withMessages(['image' => 'Não foi possível salvar a foto. Tente novamente.']);
        }

        return $path;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
