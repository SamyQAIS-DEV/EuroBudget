import {Dispatch, SetStateAction, useCallback, useState} from 'react';

type UseToggle = [boolean, () => void, Dispatch<SetStateAction<boolean>>];

/**
 * Alterne une valeur
 */
export const useToggle = (
    initialValue?: boolean
): UseToggle => {
    const [value, setValue] = useState<boolean>(!!initialValue);

    const toggle = useCallback(() => setValue(v => !v), []);

    return [value, toggle, setValue];
};