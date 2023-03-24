import {useEffect} from 'react';

type Handler = (event: KeyboardEvent) => void;

export const useOnKeyUp = <T extends HTMLElement = HTMLElement>(
    key: string,
    handler: Handler,
): void => {

    const keyPressHandler = (event: KeyboardEvent) => {
        if (key !== event.key) {
            return;
        }
        handler(event);
    };

    useEffect(() => {
        window.addEventListener('keyup', keyPressHandler);

        return () => {
            window.removeEventListener('keyup', keyPressHandler);
        };
    }, []);
};