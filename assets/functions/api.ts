import { HttpRequestMethodEnum } from "@enums/HttpEnum";
// import { flash } from "@functions/flash";
import { AlertEnum } from "@enums/AlertEnum";

/**
 * @param {RequestInfo} url
 * @param {object} body
 * @param {string} method
 * @return {Promise<Object>}
 * @throws ApiError
 */
export const jsonFetch = async (url: RequestInfo, body?: object, method: HttpRequestMethodEnum = HttpRequestMethodEnum.GET) => {
    const params = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: method === 'GET' ? undefined : JSON.stringify(body)
    };

    const response = await fetch(url, params);
    if (response.status === 204) {
        return null;
    }
    const data = await response.json()
    if (response.ok) {
        return data;
    }
    throw new ApiError(data, response.status);
}

/**
 * @param {RequestInfo} url
 * @param {Object} body
 * @param {string} method
 * @return {Promise<Object>}
 * @throws ApiError
 */
// export const jsonFetchOrFlash = async (url: RequestInfo, body?: Object, method: HttpRequestMethodEnum = HttpRequestMethodEnum.GET) => {
//     try {
//         return await this.jsonFetch(url, body, method);
//     } catch (error) {
//         // On catch l'erreur pour vérifier si c'est une erreur API puis on rethrow derrière car dans tous les cas on recatch derrière
//         if (error instanceof ApiError && 'main' in error.violations) {
//             flash(error.name, AlertEnum.DANGER, 10);
//         }
//         throw error;
//     }
// }

/**
 * Représente une erreur d'API
 * @property {{
 *  violations: {propertyPath: string, message: string}
 * }} data
 */
export class ApiError {
    private data?: any;
    private status?: number;

    constructor (data, status) {
        this.data = data;
        this.status = status;
    }

    // Récupère la liste de violation pour un champ donné
    violationsFor (field) {
        return this.data.violations.filter(v => v.propertyPath === field).map(v => v.message);
    }

    get name () {
        return `${this.data.title}. ${this.data.detail || ''}`;
    }

    // Renvoie les violations indexées par propertyPath
    get violations () {
        if (!this.data.violations) {
            return {
                main: `${this.data.title} ${this.data.detail || ''}`
            };
        }
        return this.data.violations.reduce((acc, violation) => {
            if (acc[violation.propertyPath]) {
                acc[violation.propertyPath].push(violation.message);
            } else {
                acc[violation.propertyPath] = [violation.message];
            }
            return acc;
        }, {});
    }
}