<?php

namespace App\Http\Controllers\Api\Blog\Admin;

//use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
//use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;

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
    public function store(BlogCategoryCreateRequest $request)
    {
        // dd(__METHOD__);

        // $data = $request->all();

        // if (empty($data['slug'])) {
        //     $data['slug'] = Str::slug($data['title']);
        // }


        // try {
        //     $item = BlogCategory::create($data);
        //     return [
        //         'success' => 'Успішно збережено',
        //         'data'    => $item
        //     ];
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'msg' => 'Помилка збереження: така категорія уже існує!'
        //     ], 422);
        // }


        $data = $request->input(); //отримаємо масив даних, які надійшли з форми
        if (empty($data['slug'])) { //якщо псевдонім порожній
            $data['slug'] = Str::slug($data['title']); //генеруємо псевдонім
        }
        
        $item = (new BlogCategory())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            return [
                'success' => true,
                'message' => 'Успішно збережено'
                ];
        } else {
            return ['message' => 'Помилка збереження'];
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
    public function update(BlogCategoryUpdateRequest $request, $id)
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

        try {
            $item->update($data);
            return [
                'success' => 'Успішно збережено',
                'data'    => $item
            ];
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Помилка оновлення: ця назва уже зайнята іншою категорією!'
            ], 422);
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
