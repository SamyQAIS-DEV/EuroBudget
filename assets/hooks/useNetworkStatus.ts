import {useEffect, useState} from 'react';

export const useNetworkStatus = (): [boolean] => {
    const [networkStatus, setuseNetworkStatus] = useState<boolean>(navigator.onLine);

    useEffect(() => {
        window.addEventListener('offline', () => setuseNetworkStatus(navigator.onLine));
        window.addEventListener('online', () => setuseNetworkStatus(navigator.onLine));
    }, []);

    return [networkStatus];
};