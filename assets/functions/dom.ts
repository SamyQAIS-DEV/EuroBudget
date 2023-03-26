/**
 * Génère une classe à partir de différentes variables
 *
 * @param  {...string|null} classnames
 */
export const classNames = (...classnames: any[]): string => {
    return classnames.filter((classname) => classname !== null && classname !== false && classname !== undefined).join(' ');
};