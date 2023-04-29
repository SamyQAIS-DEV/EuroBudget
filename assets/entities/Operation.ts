import {Category} from '@entities/Category';
import {TypeEnum} from '@enums/TypeEnum';

interface OperationInterface {
    id?: number;
    label?: string;
    amount?: number;
    date?: Date;
    type?: TypeEnum;
    past?: boolean;
    category?: Category;
}

export class Operation implements OperationInterface {
    public id: number;
    public label: string;
    public amount: number;
    public date: Date;
    public type: TypeEnum;
    public past: boolean;
    category?: Category;

    constructor(options: OperationInterface = {}) {
        Object.assign(this, options);
        this.date = options.date ? new Date(options.date) : new Date();
    }
}