const currencyFormatter = new Intl.NumberFormat(undefined, {
    currency: 'eur',
    style: 'currency',
    minimumFractionDigits: 2,
});

export const formatCurrency = (number: number): string => {
    return currencyFormatter.format(number);
};

// export const calculate = (a, b, type: TypeEnum) => {
// return type === TypeEnum.DEBIT ? Number(a) - Number(b) : Number(a) + Number(b);
// };