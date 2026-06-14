<?php

namespace App\Http\Resources\Api\Blog\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PostResource extends JsonResource
{
    /**
     * Трансформація ресурсу в масив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $publishedAt = $this->published_at ? Carbon::parse($this->published_at) : null;
        $createdAt   = $this->created_at ? Carbon::parse($this->created_at) : null;
        $updatedAt   = $this->updated_at ? Carbon::parse($this->updated_at) : null;

        // $this вказує на поточний об'єкт моделі BlogPost
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'is_published'   => (bool) $this->is_published,
            'user_id'        => $this->user_id,
            'category_id'    => $this->category_id,
            
            'date_published' => $publishedAt ? $publishedAt->format('Y-m-d H:i:s') : null,
            // 'published_at'   => $publishedAt ? $publishedAt->format('Y-m-d H:i:s') : null,

            'excerpt'        => $this->whenNotNull($this->excerpt),
            'content_raw'    => $this->whenNotNull($this->content_raw),
            'content_html'   => $this->whenNotNull($this->content_html),
            
            'created_at'     => $createdAt ? $createdAt->format('Y-m-d H:i:s') : null,
            'updated_at'     => $updatedAt ? $updatedAt->format('Y-m-d H:i:s') : null,

            'user'           => [
                'id'                => $this->user_id,
                'name'              => $this->user?->name ?? 'ID ' . $this->user_id,
                'profile_photo_url' => $this->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->user?->name ?? 'A'),
            ],
            
            'category'       => [
                'id'    => $this->category_id,
                'title' => $this->category?->title ?? 'Без категорії'
            ],
        ];
    }
}