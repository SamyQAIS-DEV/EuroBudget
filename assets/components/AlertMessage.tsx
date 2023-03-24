import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';

type AlertMessageProps = {
    type?: string;
    duration?: number;
    floating?: boolean;
} & PropsWithChildren & HTMLProps<HTMLDivElement>;

export const AlertMessage = ({
    type,
    duration,
    floating,
    className,
    children,
    ...props
}: AlertMessageProps) => {
    className = classNames(className, 'alert-message');

    return (
        <div className={className} {...props}>
            {children}
        </div>
    );
};