import {ComponentsTestingElement} from './ComponentsTestingElement';
import {SwitchElement} from './SwitchElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';
import {NetworkStatusElement} from './NetworkStatusElement';

// Custom Elements
customElements.define('components-testing', ComponentsTestingElement);
customElements.define('theme-switcher', ThemeSwitcherElement);
customElements.define('network-status', NetworkStatusElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })