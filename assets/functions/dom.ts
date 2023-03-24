/**
 * Génère une classe à partir de différentes variables
 *
 * @param  {...string|null} classnames
 */
export const classNames = (...classnames: string[]): string => {
    return classnames.join(' ');
};