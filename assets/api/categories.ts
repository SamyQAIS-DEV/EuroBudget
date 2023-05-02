import {jsonFetch} from '@functions/api';
import {Category} from '@entities/Category';

export const findCategories = async (): Promise<Category[]> => {
    const categories = await jsonFetch<Category[]>('/api/categories');
    return categories.map((category) => new Category(category));
}