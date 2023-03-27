import {useCallback, useEffect, useState} from 'react';
import {ApiError, jsonFetch, jsonFetchOrFlash} from '@functions/api';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';
import {addFlash} from '@elements/AlertElement';
import {AlertEnum} from '@enums/AlertEnum';

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

export const useJsonFetch = <T>(
    autoFetch: boolean,
    url: string,
    body?: object,
    method?: HttpRequestMethodEnum
): [State<T>, FetchFn<T>] => {
    const [state, setState] = useState<State<T>>({
        data: null,
        isLoading: autoFetch,
        isError: false,
        isDone: false
    });

    const fetch: FetchFn<T> = useCallback(
        async (localUrl?: string, localBody?: object, localMethod?: HttpRequestMethodEnum) => {
            setState(s => ({ ...s, isLoading: true, isError: false, isDone: false }));
            try {
                const response = await jsonFetch<T>(localUrl || url, localBody || body, localMethod || method);
                setState(s => ({ ...s, data: response, isLoading: false, isError: false, isDone: true }));
                return response;
            } catch (error) {
                if (error instanceof ApiError && 'main' in error.violations) {
                    addFlash(error.name, AlertEnum.ERROR);
                }
            }
            setState(s => ({ ...s, isLoading: false, isError: true, isDone: true }));
        },
        [url, body]
    );

    useEffect(() => {
        if (autoFetch) {
            fetch();
        }
    }, []);

    return [{...state}, fetch];
};