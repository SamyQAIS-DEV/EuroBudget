import React, {ComponentProps} from 'react';
import {classNames} from '@functions/dom';

type IconProps = {
    show: boolean;
    closable?: boolean;
    onClose: () => void;
} & ComponentProps<any>;

export const Icon = ({
    name,
    size,
    className,
    ...props
}: IconProps) => {
    const href = `/sprite.svg#${name}`;
    className = classNames(className, `icon icon-${name}`);

    return (
        <svg className={className} width={size} height={size} {...props}>
            <use xlinkHref={href} />
        </svg>
    );
};