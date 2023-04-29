import {jsonFetch} from '@functions/api';
import {YearMonth} from '@entities/YearMonth';

export async function findYearsMonths(): Promise<YearMonth[]> {
    const yearMonths = await jsonFetch<YearMonth[]>('/api/operations/years-months');
    return yearMonths.map((yearMonth) => new YearMonth(yearMonth));
}