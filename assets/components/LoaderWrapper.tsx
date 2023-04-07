import React from 'react';
import {Animated} from '@components/Animation/Animated';
import {Loader} from '@components/Animation/Loader';

export const LoaderWrapper = () => {

    return (
        <Animated show={true} animationName="fade" className="loader-wrapper">
            <Loader/>
        </Animated>
    );
};