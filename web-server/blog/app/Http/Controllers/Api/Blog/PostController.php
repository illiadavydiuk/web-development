<?php

namespace App\Http\Controllers\Api\Blog;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Repositories\BlogPostRepository;

class PostController extends BaseController
{
    public function __construct(private BlogPostRepository $blogPostRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->only(['page', 'per_page', 'search']);
        
        // Отримуємо відфільтрований пагінатор
        $paginator = $this->blogPostRepository->getAllWithPaginate($params);

        return $paginator;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
