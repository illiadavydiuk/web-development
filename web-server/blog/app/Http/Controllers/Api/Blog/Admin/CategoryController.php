<?php

namespace App\Http\Controllers\Api\Blog\Admin;

//use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Repositories\BlogCategoryRepository;
//use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;
use App\Http\Controllers\Api\Blog\BaseController;
use App\Http\Resources\Api\Blog\Admin\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(private BlogCategoryRepository $blogCategoryRepository)
    {
        //parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */

    
    public function index(Request $request)
    {
//        dd(__METHOD__);
        $params = $request->all();

        $paginator = $this->blogCategoryRepository->getAllWithPaginate($params);

        return CategoryResource::collection($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми
        
        
        $item = (new BlogCategory())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            return [
                'success' => true,
                'message' => 'Успішно збережено',
                'item' => $item
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
        $item = $this->blogCategoryRepository->getEdit($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }
        return new CategoryResource($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        // dd(__METHOD__);

        // $item = BlogCategory::find($id);
        $item = $this->blogCategoryRepository->getEdit($id);
        
        if (empty($item)) {
            return response()->json([
                'msg' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        $data = $request->all(); // Отримаємо масив даних, які надійшли з форми

        $result = $item->update($data);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Успішно оновлено',
                'item' => $item];
        } else {
            return [
                'success' => false,
                'message' => 'Помилка оновлення'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(string $id)
    {
        $item = BlogCategory::find($id);

        if (!$item) {
            return response()->json(['message' => "Запис id=[{$id}] не знайдено"], 404);
        }

        $item->delete();


        return response()->json([
            'success' => true,
            'message' => 'Успішно видалено',
            'data'    => $item 
        ]);
    }
}
