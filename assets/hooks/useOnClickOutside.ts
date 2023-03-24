import {RefObject, useEffect} from 'react';

type Handler = (event: MouseEvent) => void;

export const useOnClickOutside = <T extends HTMLElement = HTMLElement>(
    mouseEvent: 'mousedown' | 'mouseup' = 'mousedown',
    ref: RefObject<T>,
    handler: Handler,
): void => {

    const mouseEventHandler = (event: MouseEvent) => {
        const element = ref?.current;
        if (!element) {
            return;
        }

        if (element.contains(event.target as Node)) {
            return;
        }

        handler(event);
    };

    useEffect(() => {
        window.addEventListener(mouseEvent, mouseEventHandler);

        return () => {
            window.removeEventListener(mouseEvent, mouseEventHandler);
        };
    }, []);
};