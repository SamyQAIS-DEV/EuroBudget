import React, {HTMLProps, MouseEventHandler, PropsWithChildren, useEffect, useRef} from 'react';
import {Animated} from '@components/Animation/Animated';
import {classNames} from '@functions/dom';
import {useOnClickOutside} from '@hooks/useOnClickOutside';
import {useOnKeyUp} from '@hooks/useOnKeyUp';
import {Icon} from '@components/Icon';
import {createPortal} from 'react-dom';

type ModalProps = {
    show: boolean;
    title?: string;
    closable?: boolean;
    onClose?: () => void;
} & PropsWithChildren & HTMLProps<HTMLDivElement>;

export const Modal = ({
    show,
    title,
    closable = true,
    onClose,
    className,
    children,
}: ModalProps) => {
    const ref = useRef(null);
    const wrapperClassName = 'modal-dialog';
    className = classNames('modal-box', className);

    if (closable && onClose === undefined) {
        console.error('Modal Component : onClose can\'t be empty if the modal is closable');
    }

    useEffect(() => {
        if (show) {
            document.body.classList.add('locked');
            return;
        }
        document.body.classList.remove('locked');
    }, [show]);

    const handleClose = () => {
        if (closable) {
            onClose();
        }
    };

    const handleClickOutside = (event: MouseEvent) => {
        if (event.target === event.currentTarget) {
            handleClose();
        }
    };

    useOnKeyUp('Escape', handleClose);
    // useOnClickOutside('mousedown', ref, handleClickOutside);

    return createPortal(
        <Animated show={show} animationName="modal" className={wrapperClassName} onClick={handleClickOutside}>
            <Animated show={show} animationName="modalSlide" className={className} forwardedRef={ref}>
                {closable === true && (
                    <button className="modal-close" onClick={handleClose}>
                        <Icon name="x"/>
                    </button>
                )}
                {title && <h2 className="modal-title" dangerouslySetInnerHTML={{__html: title}}/>}
                {children}
            </Animated>
        </Animated>,
        document.querySelector('body'),
    );
};