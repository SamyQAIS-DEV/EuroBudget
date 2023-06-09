import React from 'react';
import {classNames} from '@functions/dom';
import {formatCurrency} from '@functions/number';
import {DepositAccount as DepositAccountEntity} from '@entities/DepositAccount';
import {Icon} from '@components/Icon';

type DepositAccountProps = {
    depositAccount: DepositAccountEntity;
    isSelected?: boolean;
};

export const DepositAccount = ({
    depositAccount,
    isSelected = false,
}: DepositAccountProps) => {
    const className = classNames('deposit-account', isSelected && 'selected');
    const amountClassName = classNames('deposit-account__amount', depositAccount.amount >= 0 ? 'positive' : 'negative');

    return (
        <div className={className}>
            <div className="deposit-account__title">
                <span className='deposit-account__color-indicator' style={{background: depositAccount.color}}></span><span>{depositAccount.title}</span>
            </div>
            <div className={amountClassName}>{formatCurrency(depositAccount.amount)}</div>
            <a href={`/deposit-accounts/${depositAccount.id}/edit`} className="btn-primary" data-turbolinks="false"><Icon name='edit'/></a>
            {isSelected && <Icon className="selected-icon" name="check"/>}
        </div>
    );
};