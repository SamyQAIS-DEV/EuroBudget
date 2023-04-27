import {jsonFetchOrFlash} from '@functions/api';
import {Operation} from '@entities/Operation';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';

export async function findOperationsForCurrentMonth(): Promise<Operation[]> {
    const operations = await jsonFetchOrFlash<Operation[]>('/api/operations/current-month');
    return operations.map((operation) => new Operation(operation));
}

export async function findOperationsForMonth(year: string, month: string): Promise<Operation[]> {
    const operations = await jsonFetchOrFlash<Operation[]>(`/api/operations/${year}/${month}`);
    return operations.map((operation) => new Operation(operation));
}

export async function addOperation(operation: Operation): Promise<Operation> {
    const newOperation = await jsonFetchOrFlash<Operation>('/api/operations', operation, HttpRequestMethodEnum.POST);
    return new Operation(newOperation);
}

export async function updateOperation(operation: Operation): Promise<Operation> {
    const newOperation = await jsonFetchOrFlash<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
    return new Operation(newOperation);
}

export async function updatePastOperation(operation: Operation): Promise<Operation> {
    const newOperation = await jsonFetchOrFlash<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
    return new Operation(newOperation);
}

export async function deleteOperation(operation: Operation): Promise<void> {
    return await jsonFetchOrFlash<void>(`/api/operations/${operation.id}`, {}, HttpRequestMethodEnum.DELETE);
}