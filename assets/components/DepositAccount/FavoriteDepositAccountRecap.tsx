import React, {useState} from 'react';
import {DepositAccountRecap} from '@components/DepositAccount/DepositAccountRecap';
import {MonthsList} from '@components/MonthsList';
import {OperationList} from '@components/Operation/OperationList';
import {Operation as OperationEntity} from '@entities/Operation';
import {isCurrentMonth} from '@functions/date';

type Props = {
    labels: string[];
};

export const FavoriteDepositAccountRecap = ({labels}: Props) => {
    const [operationChanged, setOperationChanged] = useState<OperationEntity>(null);
    const [otherMonthAdded, setOtherMonthAdded] = useState<Date>(null);
    const [year, setYear] = useState<string>(null);
    const [month, setMonth] = useState<string>(null);

    const handleMonthSelected = (year: string, month: string) => {
        setYear(year);
        setMonth(month);
    };

    const handleOperationChanged = (operation: OperationEntity) => {
        setOperationChanged(operation);
        if (!isCurrentMonth(operation.date)) {
            setOtherMonthAdded(operation.date);
        }
    };

    const style = {};
    style['--gap'] = 5;

    return (
        <div className="stack mt3" style={style}>
            <DepositAccountRecap operationChanged={operationChanged}/>
            <MonthsList onSelect={handleMonthSelected} otherMonthAdded={otherMonthAdded}/>
            <OperationList year={year} month={month} labels={labels} onOperationChanged={handleOperationChanged}/>
        </div>
    );
};