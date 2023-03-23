import {useEffect, useState} from 'react';
import {cookie} from '@functions/cookie';

type UpdateCookieFn = (
    name: string,
    value?: string,
    expiresInDays?: number
) => void;

export const useCookie = (name: string, defaultValue?: string): [string, UpdateCookieFn] => {
    const getCookie = () => cookie(name) || defaultValue;
    const [theCookie, setCookie] = useState<string>(getCookie());

    const updateCookie: UpdateCookieFn = async (
        name,
        value,
        expiresInDays
    ) => {
        setCookie(value);
        cookie(name, value, expiresInDays);
    }

    return [theCookie, updateCookie];
};