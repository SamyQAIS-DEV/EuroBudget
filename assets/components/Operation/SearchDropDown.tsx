import React, {FormEvent, useContext, useEffect, useRef, useState} from 'react';
import {classNames} from '@functions/dom';
import {AttrProps} from '@components/Form/Field';
import {Animated} from '@components/Animation/Animated';
import {useOnClickOutside} from '@hooks/useOnClickOutside';
import {FormContext} from '@components/Form/Form';

type Props = {
    label: string;
    values: string[];
    onChange: (event: FormEvent) => void;
    onSelection: (value: string) => void;
} & AttrProps;

const SearchDropDown = ({values, onChange, onSelection, ...props}: Props) => {
    const {errors, emptyError, isLoading} = useContext(FormContext);
    const error = errors[props.name] || null;
    const ref = useRef(null);
    const dropdownRef = useRef(null);
    const inputRef = useRef(null);
    const [search, setSearch] = useState<string>('');
    const [isVisible, setVisible] = useState<boolean>(false);
    const [index, setIndex] = useState<number>(0);
    const [items, setItems] = useState<string[]>(values);

    const className = classNames(props.className, 'form-group search-dropdown');

    useEffect(() => {
        const handler = (event: KeyboardEvent) => {
            if (!isVisible) {
                return;
            }
            if (event.key === 'ArrowDown') {
                setIndex(prevIndex => select(prevIndex + 1));
            }
            if (event.key === 'ArrowUp') {
                setIndex(prevIndex => select(prevIndex - 1));
            }
            if (event.key === 'Home') {
                setIndex(0);
            }
            if (event.key === 'End') {
                setIndex(items.length - 1);
            }
        };

        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [isVisible, items]);

    useEffect(() => {
        const li = dropdownRef.current.querySelector(`li:nth-child(${index})`);
        if (li) {
            // li.scrollIntoView({ behavior: 'smooth', block: 'center' });
            dropdownRef.current.scrollTop = li.offsetTop - dropdownRef.current.offsetHeight / 2;
        }

        const handler = (event: KeyboardEvent) => {
            if (event.key === 'Enter' && index !== 0) {
                event.preventDefault();
                const item = items[index - 1];
                if (!item) {
                    return;
                }
                handleSelect(item);
            }
            if (event.key === 'Tab') {
                setVisible(false);
                return;
            }
        };

        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [index]);

    useEffect(() => {
        const newItems = values.filter(item => {
            if (item.toLowerCase()
                .startsWith(search.toLowerCase())) {
                return item;
            }
        });
        setItems(newItems);
    }, [search]);

    const handleClickOutside = () => {
        setVisible(false);
    };

    useOnClickOutside('mousedown', ref, handleClickOutside);

    const handleChange = (event: FormEvent<HTMLInputElement>) => {
        const target = event.target as HTMLInputElement;
        setVisible(true);
        setSearch(target.value);
        setIndex(0);
        onChange(event);
    };

    const handleSelect = (value: string) => {
        inputRef.current.value = value;
        onSelection(value);
        setSearch('');
        setIndex(0);
        setVisible(false);
    };

    const select = (idx): number => {
        if (idx < 0) {
            return items.length;
        } else if (idx > items.length) {
            return 1;
        } else {
            return idx;
        }
    };

    return (
        <div ref={ref} className={className}>
            <input ref={inputRef} onChange={handleChange} onInput={() => emptyError(props.name)} onClick={() => setVisible(true)} autoComplete='off' placeholder=' ' {...props}/>
            <label title={props.label} htmlFor={props.name}></label>
            {error && <div className="invalid-feedback">{error}</div>}
            <div ref={dropdownRef} className="dropdown">
                {items.length > 0 && (
                    <Animated show={isVisible} animationName="slide">
                        <ul>
                            {items.map((item, key) => {
                                return (
                                    <li key={key} aria-selected={key + 1 === index} onClick={() => handleSelect(item)}>
                                        {item}
                                    </li>
                                );
                            })}
                        </ul>
                    </Animated>
                )}
            </div>
        </div>
    );
};

export default SearchDropDown;