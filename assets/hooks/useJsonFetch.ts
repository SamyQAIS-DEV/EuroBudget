import {useCallback, useState} from 'react';
import {jsonFetch} from '@functions/api';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';

type FetchFn<T> = (
    localUrl?: string,
    localParams?: object,
    localMethod?: HttpRequestMethodEnum
) => Promise<T>;

type State<T> = {
    data?: T;
    isLoading: boolean;
    isError: boolean;
    isDone: boolean;
};

export const useJsonFetch = <T>(url: string, params: object = {}, method?: HttpRequestMethodEnum): [State<T>, FetchFn<T>] => {
    const [state, setState] = useState<State<T>>({
        data: null,
        isLoading: false,
        isError: false,
        isDone: false
    });

    const fetch: FetchFn<T> = useCallback(
        async (localUrl?: string, localParams?: object, localMethod?: HttpRequestMethodEnum) => {
            setState(s => ({ ...s, isLoading: true, isError: false, isDone: false }));
            try {
                const response = await jsonFetch<T>(localUrl || url, localParams || params, localMethod || method);
                setState(s => ({ ...s, data: response, isLoading: false, isError: false, isDone: true }));
                return response;
            } catch (e) {
                setState(s => ({ ...s, isLoading: false, isError: true, isDone: true }));
            }
            setState(s => ({ ...s, isLoading: false, isError: false, isDone: true }));
        },
        [url, params]
    );

    return [{...state}, fetch];
};