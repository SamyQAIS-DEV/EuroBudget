import {ComponentsTestingElement} from './ComponentsTestingElement';
import {AlertMessageElement} from './AlertMessageElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';
import {NetworkStatusElement} from './NetworkStatusElement';
import {SwitchElement} from './SwitchElement';
import {AutoSubmitElement} from './AutoSubmitElement';

// Custom Elements
customElements.define('components-testing', ComponentsTestingElement);
customElements.define('alert-message', AlertMessageElement);
customElements.define('theme-switcher', ThemeSwitcherElement);
customElements.define('network-status', NetworkStatusElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })
customElements.define('auto-submit', AutoSubmitElement, { extends: 'form' })