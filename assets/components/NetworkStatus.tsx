import React from 'react';
import {useNetworkStatus} from '@hooks/useNetworkStatus';
import {Modal} from '@components/Modal/Modal';

export const NetworkStatus = () => {
    const [networkStatus] = useNetworkStatus();

    return (
        <Modal title='Aucune connexion :(' icon='wifi-off' show={!networkStatus} closable={false} />
    );
};