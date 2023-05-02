import React, {CSSProperties, PropsWithChildren, Ref, useEffect, useState} from 'react';

type AnimatedProps = {
    show: boolean;
    animationName: string;
    duration?: number;
    style?: CSSProperties;
    forwardedRef?: Ref<HTMLDivElement>
} & PropsWithChildren<any>;

export const Animated = ({
    show,
    animationName,
    duration = 300,
    style = {},
    forwardedRef = null,
    className,
    children,
    ...props
}: AnimatedProps) => {
    const [shouldRender, setRender] = useState<boolean>(show);
    style = {
        animation: `${duration}ms ease-in-out ${show ? `${animationName}In` : `${animationName}Out`} both`,
        ...style,
    };

    const onAnimationEnd = (e): void => {
        if (!show && e.animationName === `${animationName}Out`) setRender(false);
    };

    useEffect(() => {
        if (show) setRender(true);
    }, [show]);

    return (
        shouldRender && (
            <div
                className={className}
                style={style}
                onAnimationEnd={onAnimationEnd}
                ref={forwardedRef}
                {...props}
            >
                {children}
            </div>
        )
    );
};