import {CommentsElement} from './CommentsElement';
import {SwitchElement} from './SwitchElement';
import {ThemeSwitcherElement} from './ThemeSwitcherElement';

// Custom Elements
customElements.define('comments-area', CommentsElement);
customElements.define('theme-switcher', ThemeSwitcherElement);

// CustomElement étendus
customElements.define('input-switch', SwitchElement, { extends: 'input' })