import {Category} from '@entities/Category';

interface OperationInterface {
    id?: number;
    label?: string;
    amount?: number;
    date?: Date;
    type?: string;
    past?: boolean;
    category?: Category;
}

export class Operation implements OperationInterface {
    public id: number;
    public label: string;
    public amount: number;
    public date: Date;
    public type: string;
    public past: boolean;
    category?: Category;

    constructor(options: OperationInterface = {}) {
        Object.assign(this, options);
        this.date = options.date ? new Date(options.date) : new Date();
    }
}