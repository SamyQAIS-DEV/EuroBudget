import {HttpRequestMethodEnum, HttpResponseCodeEnum} from '@enums/HttpEnum';
import {AlertEnum} from '@enums/AlertEnum';
import {addFlash} from '@elements/AlertElement';

const headers: HeadersInit = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

/**
 * @param {RequestInfo} url
 * @param body
 * @param {string} method
 * @return {Promise<Object>}
 * @throws ApiError
 */
export const jsonFetch = async <T extends unknown>(url: RequestInfo, body: object = {}, method: HttpRequestMethodEnum = HttpRequestMethodEnum.GET): Promise<T> => {
    const params: RequestInit = {
        headers: headers,
        method: method,
        body: method === HttpRequestMethodEnum.GET ? undefined : JSON.stringify(body),
    };

    const response = await fetch(url, params);
    if (response.status === 204) {
        return null;
    }
    if (response.ok) {
        return await response.json() as T;
    }
    const data = await response.json();
    if (response.status === HttpResponseCodeEnum.HTTP_MOVED_PERMANENTLY && data.redirect) {
        window.location.href = data.redirect;
    }
    throw new ApiError(data, response.status);
};

/**
 * @param {RequestInfo} url
 * @param {Object} body
 * @param {string} method
 * @return {Promise<Object>}
 * @throws ApiError
 */
export const jsonFetchOrFlash = async <T extends unknown>(url: RequestInfo, body?: Object, method: HttpRequestMethodEnum = HttpRequestMethodEnum.GET): Promise<T> => {
    try {
        return await jsonFetch<T>(url, body, method);
    } catch (error) {
        if (error instanceof ApiError) {
            addFlash(error.name, AlertEnum.ERROR);
        } else {
            addFlash(error.detail, AlertEnum.ERROR);
        }
        throw error;
    }
};

/**
 * Représente une erreur d'API
 * @property {{
 *  violations: {propertyPath: string, message: string}
 * }} data
 */
export class ApiError {
    private data?: any;
    public status?: number;

    constructor(data, status) {
        this.data = data;
        this.status = status;
    }

    // Récupère la liste de violation pour un champ donné
    violationsFor(field) {
        return this.data.violations.filter(v => v.propertyPath === field)
            .map(v => v.message);
    }

    get name() {
        if (this.data.title || this.data.detail) {
            return `${this.data.title}\n${this.data.detail || ''}`;
        }
        return '';
    }

    // Renvoie les violations indexées par propertyPath
    get violations() {
        if (!this.data.violations) {
            return {
                main: `${this.data.title} ${this.data.detail || ''}`,
            };
        }

        return this.data.violations.reduce((acc, violation) => {
            if (acc[violation.propertyPath]) {
                acc[violation.propertyPath].push(violation.title);
            } else {
                acc[violation.propertyPath] = [violation.title];
            }
            return acc;
        }, {});
    }
}