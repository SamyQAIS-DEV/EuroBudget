import React from 'react';
import {AttrProps} from '@components/Form/Field';
import {DepositAccount} from '@entities/DepositAccount';

type Props = {
    values: DepositAccount[];
} & AttrProps;

export function DepositAccountRadios({values, ...props}: Props) {

    return values.map((depositAccount: DepositAccount) => (
        <div className='deposit-account deposit-account-radio' key={depositAccount.id}>
            <label
                htmlFor={String(depositAccount.id)}
            >
                {depositAccount.title}
            </label>
            <input
                type="radio"
                id={String(depositAccount.id)}
                defaultChecked={depositAccount.id === props.defaultValue ? true : undefined}
                name={props.name}
                value={depositAccount.id}
            />
        </div>
    ));
}
