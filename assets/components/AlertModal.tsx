import React, {PropsWithChildren} from 'react';
import {Modal} from '@components/Modal';
import {classNames} from '@functions/dom';

type AlertModalProps = {
    show: boolean;
    onClose: () => void;
    className?: string;
} & PropsWithChildren;

export const AlertModal = ({
    show,
    onClose,
    className,
    children,
}: AlertModalProps) => {
    className = classNames('modal-alert', className);

    return (
        <Modal show={show} closable={false} className={className}>
            {children}
            <button className="btn btn-primary" onClick={onClose}>OK</button>
        </Modal>
    );
};