export const registerColor = () => {
    const color = window.eurobudget.FAVORITE_DEPOSIT_ACCOUNT_COLOR;
    if (!color) {
        return;
    }
    document.body.style.setProperty('--contrast', color, 'important');
};