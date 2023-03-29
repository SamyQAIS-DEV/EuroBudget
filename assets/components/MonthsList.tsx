import React, {useEffect, useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';
import {classNames} from '@functions/dom';
import {jsonFetch} from '@functions/api';

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
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<YearMonth[]>(true, () => jsonFetch<YearMonth[]>('/api/operations/years-months')); // TODO Use api file
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
        <section id="months-list">
            <div className="tabs years mb1 overflow-visible">
                {data.map((yearMonth: YearMonth) => {
                    const year = yearMonth.path.split('/')[0];
                    if (!renderedYears.includes(year)) {
                        renderedYears = [...renderedYears, year];
                        return (
                            <MonthsListItem
                                key={'year_' + year}
                                label={year}
                                count={yearMonth.count}
                                active={year === selectedYear} onSelect={handleYearSelected}
                            />
                        );
                    }
                } )}
            </div>
            <div className="tabs months overflow-visible">
                {data.map((yearMonth: YearMonth) => {
                    const year = yearMonth.path.split('/')[0];
                    const month = yearMonth.path.split('/')[1];
                    if (year === selectedYear) {
                        return (
                            <MonthsListItem
                                key={'month_' + month}
                                label={month}
                                count={yearMonth.count}
                                active={month === selectedMonth} onSelect={handleMonthSelected}
                            />
                        );
                    }
                } )}
            </div>
        </section>
    );
};

type MonthsListItemProps = {
    label: string;
    count: number;
    active: boolean;
    onSelect: (year: string) => void;
};

const MonthsListItem = ({label, count, active, onSelect}: MonthsListItemProps) =>  {
    return (
        <div key={'year_' + label}
             className={classNames('tab year relative', active && 'active')}
             onClick={() => onSelect(label)}
        >
            <span className='bullet'>{count}</span>
            {label}
        </div>
    );
};