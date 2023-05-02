import React, {useEffect, useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Loader} from '@components/Animation/Loader';
import {Button} from '@components/Button';
import {classNames} from '@functions/dom';
import {YearMonth} from '@entities/YearMonth';
import {findYearsMonths} from '@api/years-months';
import {Logger} from 'sass';

type Props = {
    onSelect: (year: string, month: string) => void;
    otherMonthAdded?: Date;
};

export const MonthsList = ({
    onSelect,
    otherMonthAdded,
}: Props) => {
    const [{data, isLoading, isError, isDone}, fetch, setData] = useJsonFetch<YearMonth[]>(true, findYearsMonths);
    const [selectedYear, setSelectedYear] = useState<string>(null);
    const [selectedMonth, setSelectedMonth] = useState<string>(null);

    let renderedYears: string[] = [];

    useEffect(() => {
        const now = new Date();
        setSelectedYear(String(now.getFullYear()));
        setSelectedMonth(String(((now.getMonth() + 1) < 10 && '0') + (now.getMonth() + 1)));
    }, []);

    useEffect(() => {
        if (!otherMonthAdded) {
            return;
        }
        const path = String(otherMonthAdded.getFullYear()) + '/' + String(((otherMonthAdded.getMonth() + 1) < 10 && '0') + (otherMonthAdded.getMonth() + 1));
        if (!data.some((item) => item.path === path)) {
            setData([new YearMonth({path: path, count: 1}), ...data]);
        }
    }, [otherMonthAdded]);

    const handleYearSelected = (year: string) => {
        let month: string = selectedMonth;
        if (!data.find((yearMonth: YearMonth) => yearMonth.path === year + '/' + selectedMonth)) {
            month = data.find((yearMonth: YearMonth) => yearMonth.path.startsWith(year))
                .path
                .split('/')[1];
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
                                active={year === selectedYear} onSelect={handleYearSelected}
                            />
                        );
                    }
                })}
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
                })}
            </div>
        </section>
    );
};

type MonthsListItemProps = {
    label: string;
    active: boolean;
    count?: number;
    onSelect: (year: string) => void;
};

const MonthsListItem = ({label, active, count, onSelect}: MonthsListItemProps) => {
    console.log(label);
    return (
        <div key={'year_' + label}
             className={classNames('tab year relative', active && 'active')}
             onClick={() => onSelect(label)}
        >
            {count !== undefined ? <span className="bullet">{count}</span> : null}
            {label}
        </div>
    );
};