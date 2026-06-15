<?php

namespace App\Http\Controllers\Api\Blog\Admin;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Repositories\BlogPostRepository; 
use App\Http\Requests\BlogPostCreateRequest;

use App\Repositories\BlogCategoryRepository;
use App\Http\Requests\BlogPostUpdateRequest;
use Illuminate\Support\Str;

use App\Http\Controllers\Api\Blog\BaseController;

use App\Jobs\BlogPostAfterCreateJob;
use App\Jobs\BlogPostAfterDeleteJob;
use App\Http\Resources\Api\Blog\Admin\PostResource;

class PostController extends BaseController
{
    public function __construct(
        private BlogPostRepository $blogPostRepository, 
        private BlogCategoryRepository $blogCategoryRepository)
    {
        //parent::__construct();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginator = $this->blogPostRepository->getAllWithPaginate();

        return PostResource::collection($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogPostCreateRequest $request)
    {
        
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        $item = (new BlogPost())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            // $job = new BlogPostAfterCreateJob($item);
            // $this->dispatch($job);
            BlogPostAfterCreateJob::dispatch($item);
            return ['success' => 'Успішно збережено'];
        } else {
            return ['msg' => 'Помилка збереження'];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->blogPostRepository->getEdit($id);

        if (empty($item)) {
            return response()->json([
                'success' => false,
                'message' => "Статтю з id=[{$id}] не знайдено"
            ], 404);
        }

        return new PostResource($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogPostUpdateRequest $request, string $id)
    {
        $item = $this->blogPostRepository->getEdit($id);
        if (empty($item)) { //якщо ід не знайдено
            return ['message' => "Запис id=[{$id}] не знайдено"];
        }

        $data = $request->all(); //отримаємо масив даних, які надійшли з форми
        
            
        $result = $item->update($data); //оновлюємо дані об'єкта і зберігаємо в БД

        if ($result) {
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    
        $item = $this->blogPostRepository->getEdit($id);

        $result = BlogPost::destroy($id); //софт деліт, запис лишається

        //$result = BlogPost::find($id)->forceDelete(); //повне видалення з БД

        if ($result) {
            BlogPostAfterDeleteJob::dispatch($id)->delay(now()->addSeconds(20));
            return [
                'success' => true,
                'message' => 'Успішно видалено',
                'item' => $item
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Помилка видалення'
            ];
        }
    }
}
