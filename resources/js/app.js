import './bootstrap';

import jQuery from "jquery";
window.$ = jQuery;
window.jQuery = jQuery;

import {
    Dropdown,
    Ripple,
    initTWE,
  } from "tw-elements";

initTWE({ Dropdown, Ripple });

import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;
import "simplebar/dist/simplebar.css";

// animate css
import "animate.css";


import ResizeObserver from "resize-observer-polyfill";
window.ResizeObserver = ResizeObserver;

import Cleave from "cleave.js";
window.Cleave = Cleave;

// Drag and Drop
import dragula from "dragula/dist/dragula";
import "dragula/dist/dragula.css";
window.dragula = dragula;

// Icon
import "iconify-icon";

// SweetAlert
import Swal from "sweetalert2";
window.Swal = Swal;

// tooltip and popover
// import tippy from "tippy.js";
// import "tippy.js/dist/tippy.css";
// window.tippy = tippy;
