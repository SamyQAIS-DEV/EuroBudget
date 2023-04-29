import {jsonFetch} from '@functions/api';
import {Category} from '@entities/Category';

export async function findCategories(): Promise<Category[]> {
    const categories = await jsonFetch<Category[]>('/api/categories');
    return categories.map((category) => new Category(category));
}