import React, {HTMLProps} from 'react';
import {classNames} from '@functions/dom';

export const Loader = ({className, ...props}: HTMLProps<HTMLSpanElement>) => {
    className = classNames('loader', className);

    return (
        <span className={className} {...props}></span>
    );
};