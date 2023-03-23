import React from 'react';
import {classNames} from '@functions/dom';
import {useCopyToClipBoard} from '@hooks/useCopyToClipBoard';

type CopyType = {
    text: string;
};

export const Copy = ({text}: CopyType) => {
    const [copied, copyToClipBoard] = useCopyToClipBoard(text);

    const className = classNames('btn', copied ? 'btn-primary' : 'btn-secondary');

    return (
        <div>
            <h2>Copier dans le presse-papier</h2>
            <p>{text}</p>
            <button className={className} onClick={copyToClipBoard}>{copied ? 'Copié' : 'Copier'}</button>
        </div>
    );
};