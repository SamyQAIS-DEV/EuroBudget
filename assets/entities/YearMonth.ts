interface YearMonthInterface {
    path?: string;
    count?: number;
}

export class YearMonth implements YearMonthInterface{
    public path: string;
    public count: number;

    constructor(options: YearMonthInterface = {}) {
        Object.assign(this, options);
    }
}