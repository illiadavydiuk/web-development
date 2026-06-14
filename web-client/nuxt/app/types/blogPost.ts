export interface Post {
    id: number,
    category_id: number,
    category: PostCategory,
    user_id: number,
    user: User,
    slug: string,
    title: string,
    excerpt: string,
    content_raw: string,
    content_html: string,
    is_published: boolean,
    date_published: Date,
    created_at: Date,
    updated_at: Date,
    deleted_at: Date
}

export interface PostCategory {
    id: number,
    title: string
}

export interface User {
    id: number,
    name: string,
    profile_photo_url: string,
}