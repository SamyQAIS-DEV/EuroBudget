import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';

type SwitchProps = {
    label: string;
} & PropsWithChildren & HTMLProps<HTMLInputElement>;

export const Switch = ({
    label,
    className,
    ...props
}: SwitchProps) => {
    className = classNames(className, 'switch-checkbox');

    return (
        <label className="switch">
            <input className={className} type="checkbox" {...props} />
            <i></i>
            <span className="switch-label">{label}</span>
        </label>
    );
};