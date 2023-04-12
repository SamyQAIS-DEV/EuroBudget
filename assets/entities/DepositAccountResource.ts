interface DepositAccountResourceInterface {
    id?: number;
    title?: string;
    amount?: number;
    color?: string;
    creatorId?: number;
    finalAmount?: number;
    waitingAmount?: number;
    waitingOperationsNb?: number;
}

export class DepositAccountResource implements DepositAccountResourceInterface {
    public id: number;
    public title: string;
    public amount: number;
    public color: string;
    public creatorId: number;
    public finalAmount: number;
    public waitingAmount: number;
    public waitingOperationsNb: number;

    constructor(options: DepositAccountResourceInterface = {}) {
        Object.assign(this, options);
    }
}