export interface BlogCategory {
    id: number
    title: string
    slug: string
    parent_id: number | null
    description: string | null
}