import React, {useEffect, useId, useState} from 'react';
import {useDebounce} from '@hooks/useDebounce';

export const SearchInput = () => {
    const id = useId();
    const [value, setValue] = useState<string>('');
    const [debouncedValue, isWaiting] = useDebounce<string>(value, 400);

    useEffect(() => {
        console.log('search for : ' + debouncedValue);
    }, [debouncedValue]);

    return (
        <div>
            <label htmlFor={id}>Search</label>
            <input id={id} type="search" value={value} onChange={(e) => setValue(e.target.value)} />
            {isWaiting && <p>Waiting...</p>}
        </div>
    );
};