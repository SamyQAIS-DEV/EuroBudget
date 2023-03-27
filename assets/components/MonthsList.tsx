import React, {useEffect, useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';
import {classNames} from '@functions/dom';

type YearMonth = {
    path: string,
    count: number
};

type MonthsListProps = {
    onSelect: (year: string, month: string) => void;
};

export const MonthsList = ({
    onSelect
}: MonthsListProps) => {
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<YearMonth[]>('/api/operations/years-months');
    const [selectedYear, setSelectedYear] = useState<string>(null);
    const [selectedMonth, setSelectedMonth] = useState<string>(null);

    let renderedYears: string[] = []; // TODO Refacto that

    useEffect(() => {
        const now = new Date();
        setSelectedYear(String(now.getFullYear()));
        setSelectedMonth(String((now.getMonth() + 1) < 10 && '0' + (now.getMonth() + 1)));
    }, []);

    const handleYearSelected = (year: string) => {
        let month: string = selectedMonth;
        if (!data.find((yearMonth: YearMonth) => yearMonth.path === year + '/' + selectedMonth)) {
            month = data.find((yearMonth: YearMonth) => yearMonth.path.startsWith(year)).path.split('/')[1];
        }
        setSelectedYear(year);
        setSelectedMonth(month);
        onSelect(year, month);
    };

    const handleMonthSelected = (month: string) => {
        setSelectedMonth(month);
        onSelect(selectedYear, month);
    };

    if (isLoading) {
        return <Loader/>;
    }

    if (isError) {
        return (
            <p>
                Une erreur est survenue <Button onClick={() => fetch()}>Réessayez</Button>
            </p>
        );
    }

    return (
        <div id="months-list">
            <h2>Months List</h2>
            <div className="tabs years hide-scrollbar">
                {data.map((yearMonth: YearMonth) => {
                    const year = yearMonth.path.split('/')[0];
                    if (!renderedYears.includes(year)) {
                        renderedYears = [...renderedYears, year];
                        return (
                            <MonthsListItem
                                key={'year_' + year}
                                label={year}
                                active={year === selectedYear} onSelect={handleYearSelected}
                            />
                        );
                    }
                } )}
            </div>
            <div className="tabs months hide-scrollbar">
                {data.map((yearMonth: YearMonth) => {
                    const year = yearMonth.path.split('/')[0];
                    const month = yearMonth.path.split('/')[1];
                    if (year === selectedYear) {
                        return (
                            <MonthsListItem
                                key={'month_' + month}
                                label={month}
                                active={month === selectedMonth} onSelect={handleMonthSelected}
                            />
                        );
                    }
                } )}
            </div>
        </div>
    );
};

type MonthsListItemProps = {
    label: string;
    active: boolean;
    onSelect: (year: string) => void;
};

const MonthsListItem = ({label, active, onSelect}: MonthsListItemProps) =>  {
    return (
        <div key={'year_' + label}
             className={classNames('tab year', active && 'active')}
             onClick={() => onSelect(label)}
        >
            {label}
        </div>
    );
};