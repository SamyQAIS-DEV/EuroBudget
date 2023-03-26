import {ComponentsTestingElement} from './ComponentsTestingElement';
import {AlertElement} from './AlertElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';
import {NetworkStatusElement} from './NetworkStatusElement';
import {SwitchElement} from './SwitchElement';
import {AutoSubmitElement} from './AutoSubmitElement';
import {PremiumButtonElement} from './PremiumButtonElement';

// Custom Elements
customElements.define('components-testing', ComponentsTestingElement);
customElements.define('alert-element', AlertElement);
customElements.define('theme-switcher', ThemeSwitcherElement);
customElements.define('network-status', NetworkStatusElement);
customElements.define('premium-button', PremiumButtonElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })
customElements.define('auto-submit', AutoSubmitElement, { extends: 'form' })