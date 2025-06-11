import './bootstrap';

// Icon
import "iconify-icon";

// animate css
import "animate.css";

import {
    Dropdown,
    initTWE,
    Offcanvas
  } from "tw-elements";

initTWE({ Dropdown,  Offcanvas });

import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;
import "simplebar/dist/simplebar.min.css";

import ResizeObserver from "resize-observer-polyfill";
window.ResizeObserver = ResizeObserver;

// import Cleave from "cleave.js";
// window.Cleave = Cleave;

// Drag and Drop
// import dragula from "dragula/dist/dragula";
// import "dragula/dist/dragula.css";
// window.dragula = dragula;

// SweetAlert
import Swal from "sweetalert2";
window.Swal = Swal;

// tooltip and popover
// import tippy from "tippy.js";
// import "tippy.js/dist/tippy.css";
// window.tippy = tippy;

import './lib/alpine';
