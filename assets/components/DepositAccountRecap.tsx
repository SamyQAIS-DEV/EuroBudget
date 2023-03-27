import React, {useState} from 'react';
import {FavoriteDepositAccountRecap} from '@components/FavoriteDepositAccountRecap';
import {MonthsList} from '@components/MonthsList';
import {OperationList} from '@components/OperationList';

export const DepositAccountRecap = () => {
    const [year, setYear] = useState<string>(null);
    const [month, setMonth] = useState<string>(null);

    const handleMonthSelected = (year: string, month: string) => {
        setYear(year);
        setMonth(month);
    };

    return (
        <div id="deposit-account-recap">
            <FavoriteDepositAccountRecap/>
            <MonthsList onSelect={handleMonthSelected}/>
            <OperationList year={year} month={month}/>
        </div>
    );
};