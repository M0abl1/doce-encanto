<?php

namespace App\Http\Controllers;

use App\Models\Cake;
use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'featured' => Cake::active()->where('is_featured', true)->with('category')->latest()->take(6)->get(),
            'categories' => Category::whereHas('cakes', fn ($q) => $q->active())->orderBy('position')->get(),
        ]);
    }
}
