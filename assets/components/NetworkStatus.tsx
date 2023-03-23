import React from 'react';
import {useNetworkStatus} from '@hooks/useNetworkStatus';

export const NetworkStatus = () => {
    const [networkStatus] = useNetworkStatus();

    return (
        <div>
            <h2>Network Status</h2>
            <p>{networkStatus ? 'Online :)' : 'Offline :('}</p>
        </div>
    );
};