import React, {FormEvent, useEffect, useRef, useState} from 'react';
import {useJsonFetch} from '@hooks/useJsonFetch';
import {Icon} from '@components/Icon';
import {classNames} from '@functions/dom';
import {useDebounce} from '@hooks/useDebounce';
import {User} from '@entities/User';
import {searchUsers} from '@api/users';
import {Loader} from '@components/Animation/Loader';

const SEARCH_URL = '/recherche';

type SearchInputProps = {
    defaultValue?: string;
};

export const SearchInput = ({defaultValue = ''}: SearchInputProps) => {
    const input = useRef(null);
    const [search, setSearch] = useState(defaultValue);
    const [{data, isLoading, isError, isDone}, fetch] = useJsonFetch<User[]>(false);
    const [selectedItem, setSelectedItem] = useState(null);
    const [debouncedValue, isWaiting] = useDebounce<string>(search, 400);

    let results = data?.length > 0 ? data : [];
    if (search === '') {
        results = [];
    }

    useEffect(() => {
        input.current.focus();
    }, []);

    useEffect(() => {
        if (search === '') {
            return;
        }
        fetch(() => searchUsers(search));
    }, [debouncedValue]);

    const onSubmit = (e: FormEvent) => {
        if (selectedItem !== null && results[selectedItem]) {
            e.preventDefault();
            window.location.href = `/profil/${results[selectedItem].id}`;
        }
    };

    return (
        <form action={SEARCH_URL} onSubmit={onSubmit} className="search-input form-group" onClick={e => e.stopPropagation()}>
            <input
                autoFocus
                type="text"
                name="q"
                ref={input}
                autoComplete="off"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Rechercher un utilisateur..."
            />
            <small className='text-muted'>Nom et/ou prénom exact(s)</small>
            <button type="submit">
                <Icon name="search"/>
            </button>
            {isLoading && <Loader className="search-input_loader"/>}
            {results.length > 0 && (
                <ul className="search-input_suggestions">
                    {results.map((r, index) => (
                        <li key={r.id}>
                            <a className={classNames(index === selectedItem && 'focused')} href={`/profil/${r.id}`}>
                                <span>{r.fullName} - {r.email}</span>
                            </a>
                        </li>
                    ))}
                </ul>
            )}
        </form>
    );
};