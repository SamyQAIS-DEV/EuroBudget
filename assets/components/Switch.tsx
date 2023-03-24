import React, {ComponentProps} from 'react';
import {classNames} from '@functions/dom';

export const Switch = ({label, className, ...props}: ComponentProps<any>) => {
    className = classNames(className, 'switch-checkbox');

    return (
        <label className="switch">
            <input className={className} type="checkbox" {...props} />
            <i></i>
            <span className='switch-label'>{label}</span>
        </label>
    );
};