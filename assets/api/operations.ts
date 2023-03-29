import {jsonFetch, jsonFetchOrFlash} from '@functions/api';
import {Operation} from '@entities/Operation';
import {HttpRequestMethodEnum} from '@enums/HttpEnum';

export async function findOperationsForCurrentMonth(): Promise<Operation[]> {
    return await jsonFetch<Operation[]>('/api/operations/current-month');
}

export async function findOperationsForMonth(year: string, month: string): Promise<Operation[]> {
    return await jsonFetch<Operation[]>(`/api/operations/${year}/${month}`);
}

export async function addOperation(operation: Operation): Promise<Operation> {
    return jsonFetch<Operation>('/api/operations', operation, HttpRequestMethodEnum.POST);
}

export async function updateOperation(operation: Operation): Promise<Operation> {
    return jsonFetch<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
}

export async function updatePastOperation(operation: Operation): Promise<Operation> {
    return jsonFetchOrFlash<Operation>(`/api/operations/${operation.id}`, operation, HttpRequestMethodEnum.PUT);
}

export async function deleteOperation(operation: Operation): Promise<void> {
    return jsonFetch<void>(`/api/operations/${operation.id}`, {}, HttpRequestMethodEnum.DELETE);
}