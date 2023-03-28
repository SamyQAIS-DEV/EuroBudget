import React from 'react';
import {useTheme} from '@hooks/useTheme';
import {Switch} from '@components/Switch';

export const ThemeSwitcher = () => {
    const {theme, isDarkTheme, toggle, enable, disable} = useTheme();

    return (
        <Switch id='theme-switcher' checked={isDarkTheme} onChange={toggle} label='Thème' />
    );
};