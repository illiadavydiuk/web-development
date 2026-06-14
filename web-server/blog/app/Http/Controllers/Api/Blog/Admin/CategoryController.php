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

class CategoryController extends BaseController
{
    public function __construct(private BlogCategoryRepository $blogCategoryRepository)
    {
        //parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */

    
    public function index()
    {
//        dd(__METHOD__);
        // $paginator = BlogCategory::paginate(5);
        $paginator = $this->blogCategoryRepository->getAllWithPaginate(5);

        // return $paginator;
        return CategoryResource::collection($paginator);
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

        // $item = BlogCategory::find($id);
        $item = $this->blogCategoryRepository->getEdit($id);
        
        if (empty($item)) {
            return response()->json([
                'msg' => "Запис id=[{$id}] не знайдено"
            ], 404);
        }

        $data = $request->all(); // Отримаємо масив даних, які надійшли з форми

        // try {
        //     $item->update($data);
        //     return [
        //         'success' => 'Успішно збережено',
        //         'data'    => $item
        //     ];
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'msg' => 'Помилка оновлення: ця назва уже зайнята іншою категорією!'
        //     ], 422);
        // }

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
        dd(__METHOD__);
    }
}
