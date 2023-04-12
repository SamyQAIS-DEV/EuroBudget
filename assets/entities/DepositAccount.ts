interface DepositAccountInterface {
    id?: number;
    title?: string;
    amount?: number;
    color?: string;
}

export class DepositAccount implements DepositAccountInterface {
    public id: number;
    public title: string;
    public amount: number;
    public color: string;

    constructor(options: DepositAccountInterface = {}) {
        Object.assign(this, options);
    }
}