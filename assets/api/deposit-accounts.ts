import {jsonFetch} from '@functions/api';
import {DepositAccountResource} from '@entities/DepositAccountResource';

export async function findDepositAccountResource(): Promise<DepositAccountResource> {
    const operation = await jsonFetch<DepositAccountResource>('/api/deposit-accounts/favorite-recap');
    return new DepositAccountResource(operation);
}