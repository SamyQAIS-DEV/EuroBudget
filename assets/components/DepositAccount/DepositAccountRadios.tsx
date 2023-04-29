import React from 'react';
import {AttrProps} from '@components/Form/Field';
import {DepositAccount as DepositAccountEntity} from '@entities/DepositAccount';
import {DepositAccount} from '@components/DepositAccount/DepositAccount';

type Props = {
    values: DepositAccountEntity[];
} & AttrProps;

export const DepositAccountRadios = ({values, ...props}: Props) => {

    return values.map((depositAccount) => (
        <div className="deposit-account-radio" key={depositAccount.id}>
            <label htmlFor={String(depositAccount.id)}>
                <DepositAccount
                    key={depositAccount.id}
                    depositAccount={depositAccount}
                    isSelected={depositAccount.id === props.defaultValue}
                />
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
