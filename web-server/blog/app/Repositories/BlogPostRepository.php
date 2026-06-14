<?php

namespace App\Repositories;

use App\Models\BlogPost as Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BlogСategoryRepository.
 */
class BlogPostRepository extends CoreRepository
{
    protected function getModelClass()
    {
        return Model::class; //абстрагування моделі BlogCategory, для легшого створення іншого репозиторія
    }

    /**
     * Отримати список статей
     * 
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPaginate($params = [])
    {
        $columns = ['id', 'title', 'slug', 'is_published', 'published_at', 'user_id', 'category_id',];
        
        $perPage = data_get($params, 'per_page', 10);
        $search = data_get($params, 'search');

        $query = $this->startConditions()
                    ->select($columns)
                    ->orderBy('id', 'DESC')
                    ->with([
                        'category' => function ($query) {
                            $query->select(['id', 'title']);
                        },
                        'user' => function ($query) {
                            $query->select(['id', 'name']);
                        },
                    ]);
        if (!empty($search)) {
            $query->where('title', 'LIKE', "%{$search}%");
        }
        
        return $query->paginate($perPage);
    }
    /**
     *  Отримати модель для редагування в адмінці
     *  @param int $id
     *  @return Model
     */
    public function getEdit($id)
    {
        return $this->startConditions()->find($id);
    }
}