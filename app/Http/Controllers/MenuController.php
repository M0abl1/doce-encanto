<?php

namespace App\Http\Controllers;

use App\Models\Cake;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim($request->text('busca')), 0, 60);
        $categories = Category::whereHas('cakes', fn ($q) => $q->active())->orderBy('position')->get();
        $current = $categories->firstWhere('slug', $request->text('categoria'));

        $cakes = Cake::active()->with('category')
            ->when($current, fn ($q) => $q->where('category_id', $current->id))
            ->when($search !== '', fn ($q) => $q->search($search))
            ->orderByDesc('is_featured')->orderBy('name')
            ->paginate(12)->withQueryString();

        return view('menu.index', compact('cakes', 'categories', 'current', 'search'));
    }

    public function show(Cake $cake): View
    {
        abort_unless($cake->is_active, 404);

        return view('menu.show', [
            'cake' => $cake->load('category'),
            'related' => Cake::active()->with('category')->where('category_id', $cake->category_id)
                ->whereKeyNot($cake->id)->inRandomOrder()->take(3)->get(),
        ]);
    }
}
