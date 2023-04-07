interface OperationInterface {
    id?: number;
    label?: string;
    amount?: number;
    date?: Date;
    type?: string;
    past?: boolean;
}

export class Operation implements OperationInterface{
    public id;
    public label;
    public amount;
    public date;
    public type;
    public past;

    constructor(options: OperationInterface = {}) {
        Object.assign(this, options);
        this.date = options.date ? new Date(options.date) : new Date();
    }
}