import {ComponentsTestingElement} from './ComponentsTestingElement';
import {SwitchElement} from './SwitchElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';

// Custom Elements
customElements.define('components-testing', ComponentsTestingElement);
customElements.define('theme-switcher', ThemeSwitcherElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })