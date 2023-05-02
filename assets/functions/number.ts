const currencyFormatter = new Intl.NumberFormat(undefined, {
    currency: 'eur',
    style: 'currency',
    minimumFractionDigits: 2,
});

export const formatCurrency = (number: number, addSign: boolean = true): string => {
    return (addSign === true && number >= 0 ? '+' : '') + currencyFormatter.format(number);
};