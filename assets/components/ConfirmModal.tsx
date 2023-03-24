import React, {HTMLProps, PropsWithChildren} from 'react';
import {Modal} from '@components/Modal';
import {classNames} from '@functions/dom';

type ConfirmModalProps = {
    show: boolean;
    onCancel: () => void;
    onConfirm: () => void;
    className?: string;
} & PropsWithChildren;

export const ConfirmModal = ({
    show,
    onCancel,
    onConfirm,
    className,
    children,
}: ConfirmModalProps) => {
    className = classNames('modal-alert', className);

    return (
        <Modal show={show} closable={false} className={className}>
            {children}
            <button className="btn btn-primary" onClick={onCancel}>Annuler</button>
            <button className="btn btn-primary" onClick={onConfirm}>Confirmer</button>
        </Modal>
    );
};