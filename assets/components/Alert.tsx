import React, {HTMLProps, PropsWithChildren, useEffect, useState} from 'react';
import {classNames} from '@functions/dom';
import {createPortal} from 'react-dom';
import {Animated} from '@components/Animation/Animated';
import {Icon} from '@components/Icon';
import {AlertEnum} from '@enums/AlertEnum';

type AlertProps = {
    type: string;
    duration?: number;
    floating?: boolean;
} & PropsWithChildren<any> & HTMLProps<HTMLDivElement>;

export const Alert = ({
    type,
    duration,
    floating = false,
    className,
    children,
}: AlertProps) => {
    const [show, setShow] = useState<boolean>(true);
    className = classNames(`alert alert-${type}`, floating && 'floating', className);
    const animationName = floating === true ? 'alertFloating' : 'slide';

    useEffect(() => {
        if (duration) {
            setTimeout(() => {
                setShow(false);
            }, duration);
        }
    }, []);

    let icon = 'info';
    if (type === AlertEnum.ERROR) {
        icon = 'alert-triangle';
    } else if (type === AlertEnum.ERROR) {
        icon = 'alert-circle';
    } else if (type === AlertEnum.SUCCESS) {
        icon = 'check';
    }

    const AlertComponent = (
        <Animated className={className} show={show} animationName={animationName}>
            <Icon name={icon} className="alert-icon"/>
            <div dangerouslySetInnerHTML={{__html: children}}></div>
            <button className="alert-close" onClick={close}>
                <Icon name="x-circle"/>
            </button>
            {duration && (
                <Animated className="alert__progress" show={show} animationName="alertProgess" duration={duration} />
            )}
        </Animated>
    );

    if (floating === true) {
        return createPortal(
            <>{AlertComponent}</>,
            document.querySelector('#floating-alerts'),
        );
    }

    return (
        <>{AlertComponent}</>
    );
};