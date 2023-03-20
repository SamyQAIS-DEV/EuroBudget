import {useCallback, useState} from 'react';

/**
 * Alterne une valeur
 */
export const useToggle = (initialValue = null) => {
    const [value, setValue] = useState<boolean>(initialValue);
    return [value, useCallback(() => setValue(v => !v), [])];
};