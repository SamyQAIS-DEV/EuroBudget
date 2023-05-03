import React, {useEffect} from 'react';
import {Icon} from '@components/Icon';
import {useToggle} from '@hooks/useToggle';
import {createPortal} from 'react-dom';
import {SearchInput} from '@components/Search/SearchInput';

export const Search = () => {
    const [isSearchVisible, toggleSearchBar] = useToggle(false);

    return (
        <>
            <button onClick={toggleSearchBar} aria-label="Rechercher">
                <Icon name="search"/>
            </button>
            {isSearchVisible && <SearchBar onClose={toggleSearchBar} />}
        </>
    );
};

type SearchBarProps = {
    onClose: () => void;
};

const SearchBar = ({onClose}: SearchBarProps) => {
    useEffect(() => {
        const handler = e => {
            if (e.key === 'Escape') {
                onClose();
            }
        };
        window.addEventListener('keyup', handler);
        return () => window.removeEventListener('keyup', handler);
    }, [onClose]);

    return createPortal(
        <div className="search-popup" onClick={onClose}>
            <SearchInput placeholder='Rechercher un utilisateur...' description='Nom et/ou prénom exact(s)'/>
        </div>,
        document.querySelector('body'),
    );
};