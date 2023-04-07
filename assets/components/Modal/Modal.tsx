import React, {HTMLProps, PropsWithChildren, useEffect, useRef} from 'react';
import {Animated} from '@components/Animation/Animated';
import {classNames} from '@functions/dom';
import {useOnClickOutside} from '@hooks/useOnClickOutside';
import {useOnKeyUp} from '@hooks/useOnKeyUp';
import {Icon} from '@components/Icon';
import {createPortal} from 'react-dom';

type ModalProps = {
    show: boolean;
    closable?: boolean;
    onClose?: () => void;
} & PropsWithChildren & HTMLProps<HTMLDivElement>;

export const Modal = ({
    show,
    closable = true,
    onClose,
    className,
    children,
}: ModalProps) => {
    const ref = useRef(null);
    const wrapperClassName = 'modal-dialog';
    className = classNames('modal-box', className);

    if (closable && onClose === undefined) {
        console.error('onClose can\'t be empty if the modal is closable');
    }

    useEffect(() => {
        if (show) {
            document.body.style.overflow = 'hidden';
            return;
        }
        document.body.style.overflow = '';
    }, [show]);

    const handleClose = () => {
        if (closable) {
            onClose();
        }
    };

    const handleClickOutside = () => {
        handleClose();
    };

    useOnKeyUp('Escape', handleClose);
    useOnClickOutside('mousedown', ref, handleClickOutside);

    return createPortal(
        <Animated show={show} animationName="modal" className={wrapperClassName}>
            <Animated show={show} animationName="modalSlide" className={className} forwardedRef={ref}>
                {closable === true && (
                    <button className="modal-close" onClick={handleClose}>
                        <Icon name="x"/>
                    </button>
                )}
                {children}
            </Animated>
        </Animated>,
        document.querySelector('body'),
    );
};