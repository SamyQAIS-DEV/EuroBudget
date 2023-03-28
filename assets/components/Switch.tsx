import React, {HTMLProps, PropsWithChildren} from 'react';
import {classNames} from '@functions/dom';

type SwitchProps = {
    id: string;
    label: string;
} & PropsWithChildren & HTMLProps<HTMLInputElement>;

export const Switch = ({
    id,
    label,
    className,
    ...props
}: SwitchProps) => {
    className = classNames('switch-checkbox', className);

    return (
        <div className="form-switch">
            <input type="checkbox" aria-label={label} {...props} id={id} className="form-check-input"/>
            <label className="form-check-label" htmlFor={id}>
                <span className="switch"></span>{label}
            </label>
        </div>
    );
};