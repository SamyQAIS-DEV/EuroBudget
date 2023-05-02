import React from 'react';
import {useNetworkStatus} from '@hooks/useNetworkStatus';
import {Modal} from '@components/Modal/Modal';
import {Icon} from '@components/Icon';

export const NetworkStatus = () => {
    const [networkStatus] = useNetworkStatus();

    return (
        <Modal show={!networkStatus} closable={false}>
            <h2><Icon name='wifi-off'/> Aucune connexion :(</h2>
        </Modal>
    );
};