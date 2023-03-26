import {useCookie} from '@hooks/useCookie';
import {useEffect} from 'react';

const COLOR_SCHEME_QUERY = '(prefers-color-scheme: dark)';
const KEY = 'theme';
const LIGHT_THEME = 'theme-light';
const DARK_THEME = 'theme-dark';

interface UseThemeOutput {
    theme: string;
    isDarkTheme: boolean;
    toggle: () => void;
    enable: () => void;
    disable: () => void;
}

export const useTheme = (): UseThemeOutput => {
    const isDarkOS = window.matchMedia(COLOR_SCHEME_QUERY).matches;
    const [theme, setTheme] = useCookie(KEY, isDarkOS ? DARK_THEME : LIGHT_THEME);

    useEffect(() => {
        const themeToRemove = theme === DARK_THEME ? LIGHT_THEME : DARK_THEME;
        const themeToAdd = theme === DARK_THEME ? DARK_THEME : LIGHT_THEME;
        document.body.classList.add(themeToAdd);
        document.body.classList.remove(themeToRemove);
    }, [theme]);

    return {
        theme,
        isDarkTheme: theme === DARK_THEME,
        toggle: () => setTheme(KEY, theme === DARK_THEME ? LIGHT_THEME : DARK_THEME),
        enable: () => setTheme(KEY, DARK_THEME),
        disable: () => setTheme(KEY, LIGHT_THEME),
    };
};