/**
 * Vérifie si l'utilisateur est connecté
 *
 * @return {boolean}
 */
export const isAuthenticated = (): boolean => {
    return window.eurobudget.USER_ID !== null;
};