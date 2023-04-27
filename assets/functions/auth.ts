/**
 * Vérifie si l'utilisateur est connecté
 *
 * @return {boolean}
 */
export const isAuthenticated = (): boolean => {
    return window.eurobudget.USER_ID !== null;
};

/**
 * Vérifie si l'utilisateur est premium
 *
 * @return {boolean}
 */
export const isPremium = (): boolean => {
    return window.eurobudget.IS_PREMIUM;
};