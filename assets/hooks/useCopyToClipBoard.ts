import {useCallback, useState} from 'react';

export const useCopyToClipBoard = (text: string): [boolean, () => void] => {
    const [copied, setCopied] = useState<boolean>(false);

    const copyToClipBoard = useCallback(async () => {
        try {
            if ('clipboard' in navigator) {
                await navigator.clipboard.writeText(text);
            } else {
                document.execCommand('copy', true, text);
            }
            setCopied(true);
            setTimeout(() => {
                setCopied(false);
            }, 5000);
        } catch (error) {
            console.error('Failed to copy :', error);
        }
    }, [text]);

    return [copied, copyToClipBoard];
};