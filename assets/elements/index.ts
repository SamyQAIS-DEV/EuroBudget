import {ComponentsTestingElement} from './ComponentsTestingElement';
import {AlertElement} from './AlertElement';
import {ModalElement} from './ModalElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';
import {NetworkStatusElement} from './NetworkStatusElement';
import {SwitchElement} from './SwitchElement';
import {AutoSubmitElement} from './AutoSubmitElement';
import {PremiumButtonElement} from './PremiumButtonElement';
import {FavoriteDepositAccountRecapElement} from './FavoriteDepositAccountRecapElement';
import {DepositAccountSelectionElement} from './DepositAccountSelectionElement';

// Custom Elements
customElements.define('components-testing', ComponentsTestingElement);
customElements.define('alert-element', AlertElement);
customElements.define('modal-element', ModalElement);
customElements.define('theme-switcher', ThemeSwitcherElement);
customElements.define('network-status', NetworkStatusElement);
customElements.define('premium-button', PremiumButtonElement);
customElements.define('favorite-deposit-account-recap', FavoriteDepositAccountRecapElement);
customElements.define('deposit-account-selection', DepositAccountSelectionElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })
customElements.define('auto-submit', AutoSubmitElement, { extends: 'form' })