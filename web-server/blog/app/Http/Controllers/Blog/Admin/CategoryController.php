<?php

namespace App\Http\Controllers\Blog\Admin;

//use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        dd(__METHOD__);
        $paginator = BlogCategory::paginate(5);

        return $paginator;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd(__METHOD__);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $item = BlogCategory::create($data);

        if ($item) {
            return [
                'success' => 'Успішно збережено',
                'data'    => $item
            ];
        } else {
            return ['msg' => 'Помилка збереження'];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd(__METHOD__);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd(__METHOD__);

        $item = BlogCategory::find($id);

        if (empty($item)) {
            return response()->json([
                'msg' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        $data = $request->all(); // Отримаємо масив даних, які надійшли з форми
        if (empty($data['slug'])) { // Якщо псевдонім порожній
            $data['slug'] = Str::slug($data['title']); // Генеруємо псевдонім
        }

        $result = $item->update($data); // Оновлюємо дані об'єкта і зберігаємо в БД

        if ($result) {
            return [
                'success' => 'Успішно збережено',
                'data'    => $item
            ];
        } else {
            return ['msg' => 'Помилка збереження'];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        dd(__METHOD__);
    }
}
