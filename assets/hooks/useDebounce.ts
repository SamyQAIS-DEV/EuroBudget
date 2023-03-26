import {useEffect, useState} from 'react';

let timeoutId;

export const useDebounce = <T>(value: T, delay: number): [T, boolean] => {
    const [debouncedValue, setDebouncedValue] = useState<T>(value);
    const [isWaiting, setIsWaiting] = useState<boolean>(false);

    useEffect(() => {
        if (timeoutId) {
            setIsWaiting(true);
            clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(() => {
            setDebouncedValue(value);
            setIsWaiting(false);
        }, delay);

        return () => {
            clearTimeout(timeoutId);
        };
    }, [value]);

    return [debouncedValue, isWaiting];
};