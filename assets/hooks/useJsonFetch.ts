import {useCallback, useEffect, useState} from 'react';
import {ApiError} from '@functions/api';
import {addFlash} from '@elements/AlertElement';
import {AlertEnum} from '@enums/AlertEnum';

type FetchFn<T> = (
    callback?: Function,
) => Promise<T>;

type DataFn<T> = (
    newData?: T,
) => void;

type State<T> = {
    data?: T;
    isLoading: boolean;
    isError: boolean;
    isDone: boolean;
};

export const useJsonFetch = <T>(
    autoFetch: boolean,
    callback?: Function,
): [State<T>, FetchFn<T>, (data: T) => void] => {
    const [state, setState] = useState<State<T>>({
        data: null,
        isLoading: autoFetch,
        isError: false,
        isDone: false,
    });

    const fetch: FetchFn<T> = useCallback(
        async (localCallback) => {
            setState(s => ({...s, isLoading: true, isError: false}));
            try {
                const response = localCallback !== undefined ? await localCallback() : await callback();
                setState(s => ({...s, data: response, isLoading: false, isError: false, isDone: true}));
                return response;
            } catch (error) {
                if (error instanceof ApiError && 'main' in error.violations) {
                    addFlash(error.name, AlertEnum.ERROR);
                }
            }
            setState(s => ({...s, isLoading: false, isError: true, isDone: true}));
        },
        [],
    );

    const setData: DataFn<T> = useCallback((newData: T) => {
        setState(s => ({...s, data: newData}));
    }, []);

    useEffect(() => {
        if (autoFetch) {
            fetch();
        }
    }, []);

    return [{...state}, fetch, setData];
};