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

export class DepositAccountResource implements DepositAccountResourceInterface{
    public id;
    public title;
    public amount;
    public color;
    public creatorId;
    public finalAmount;
    public waitingAmount;
    public waitingOperationsNb;

    constructor(options: DepositAccountResourceInterface = {}) {
        Object.assign(this, options);
    }
}