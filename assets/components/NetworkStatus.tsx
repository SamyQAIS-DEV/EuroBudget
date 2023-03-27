import React from 'react';
import {useNetworkStatus} from '@hooks/useNetworkStatus';
import {Modal} from '@components/Modal/Modal';

export const NetworkStatus = () => {
    const [networkStatus] = useNetworkStatus();

    return (
        <Modal show={!networkStatus} closable={false}>
            <p>Aucune connexion :(</p>
        </Modal>
    );
};