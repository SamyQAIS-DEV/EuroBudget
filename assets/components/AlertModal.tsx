import React, {ComponentProps} from 'react';
import {Modal} from '@components/Modal';
import {classNames} from '@functions/dom';

type AlertModalProps = {
    show: boolean;
    onClose: () => void;
} & ComponentProps<any>;

export const AlertModal = ({
    show,
    className,
    onClose,
    children
}: AlertModalProps) => {
    className = classNames('modal-alert', className);

    return (
        <Modal show={show} closable={false} className={className}>
            {children}
            <button className="btn btn-primary" onClick={onClose}>OK</button>
        </Modal>
    );
};