<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Получить список категорий с возможностью фильтрации
     */
    public function index(CategoryRequest $request): CategoryCollection
    {
        $categories = Category::query()
            ->filter($request->validated())
            ->sort($request->sort_by, $request->sort_dir)
            ->get();

        return new CategoryCollection($categories);
    }

    /**
     * Показать одну категорию
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }
}
