export const registerSelects = () => {
    const manage = (select: HTMLSelectElement) => {
        if (select.value) {
            select.classList.add('valid');
        } else {
            select.classList.remove('valid');
        }
    }

    document.querySelectorAll('select')
        .forEach((s) => {
            manage(s);
            s.addEventListener('change', () => manage(s));
        });
};