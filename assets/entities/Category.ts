interface CategoryInterface {
    id?: number;
    name?: string;
    slug?: string;
    color?: string;
}

export class Category implements CategoryInterface {
    public id: number;
    public name: string;
    public slug: string;
    public color: string;

    constructor(options: CategoryInterface = {}) {
        Object.assign(this, options);
    }
}