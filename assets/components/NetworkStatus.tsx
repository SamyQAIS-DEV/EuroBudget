import React from 'react';
import {useNetworkStatus} from '@hooks/useNetworkStatus';
import {Modal} from '@components/Modal/Modal';
import {Icon} from '@components/Icon';

export const NetworkStatus = () => {
    const [networkStatus] = useNetworkStatus();

    return (
        <Modal title='Aucune connexion<br>:(' show={!networkStatus} closable={false}>
            <h2><Icon name='wifi-off' size={75}/></h2>
        </Modal>
    );
};