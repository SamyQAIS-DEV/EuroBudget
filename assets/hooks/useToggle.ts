import {Dispatch, SetStateAction, useCallback, useState} from 'react';

/**
 * Alterne une valeur
 */
export const useToggle = (
    initialValue?: boolean
): [boolean, () => void, Dispatch<SetStateAction<boolean>>] => {
    const [value, setValue] = useState<boolean>(!!initialValue);

    const toggle = useCallback(() => setValue(v => !v), []);

    return [value, toggle, setValue];
};