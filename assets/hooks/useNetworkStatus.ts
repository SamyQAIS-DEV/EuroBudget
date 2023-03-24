import {useEffect, useState} from 'react';

export const useNetworkStatus = (): [boolean] => {
    const [networkStatus, setNetworkStatus] = useState<boolean>(navigator.onLine);

    useEffect(() => {
        window.addEventListener('offline', () => setNetworkStatus(navigator.onLine));
        window.addEventListener('online', () => setNetworkStatus(navigator.onLine));
    }, []);

    return [networkStatus];
};