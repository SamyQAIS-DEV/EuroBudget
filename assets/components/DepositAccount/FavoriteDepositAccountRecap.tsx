import React, {useState} from 'react';
import {DepositAccountRecap} from '@components/DepositAccount/DepositAccountRecap';
import {MonthsList} from '@components/MonthsList';
import {OperationList} from '@components/Operation/OperationList';
import {Operation as OperationEntity} from '@entities/Operation';

export const FavoriteDepositAccountRecap = () => {
    const [year, setYear] = useState<string>(null);
    const [month, setMonth] = useState<string>(null);

    const handleMonthSelected = (year: string, month: string) => {
        setYear(year);
        setMonth(month);
    };

    const handleOperationChanged = (operation: OperationEntity) => {
        console.log('Operation Changed', operation);
    };

    const style = {};
    style['--gap'] = 5;

    return (
        <div className="stack mt3" style={style}>
            <DepositAccountRecap/>
            <MonthsList onSelect={handleMonthSelected}/>
            <OperationList year={year} month={month} onOperationChanged={handleOperationChanged}/>
        </div>
    );
};