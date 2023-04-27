export const formatDate = (date: Date, separator: string = null, reverse: boolean = false, locale: string = null): string => {
    let loc = Intl.DateTimeFormat()
        .resolvedOptions().locale;
    if (locale) {
        loc = locale;
    }
    if (separator) {
        let formattedDate = new Intl.DateTimeFormat(loc, {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        }).format(date).split('/');

        if (reverse) {
            formattedDate = formattedDate.reverse();
        }

        return formattedDate.join(separator);
    }

    return date.toLocaleString(loc, {year: 'numeric', month: 'short', day: 'numeric'});
};

export const isCurrentMonth = (date: Date, compareWith: Date = new Date()): boolean =>  {
    return date.getFullYear() === compareWith.getFullYear() && date.getMonth() === compareWith.getMonth();
};