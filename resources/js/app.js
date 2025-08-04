import './bootstrap';

// Icon
import "iconify-icon";

// animate css
import "animate.css";

import {
    Dropdown,
    initTWE,
    Offcanvas,
    Modal,
    Ripple,
  } from "tw-elements";

initTWE({ Dropdown, Offcanvas, Modal, Ripple });

import SimpleBar from "simplebar";
window.SimpleBar = SimpleBar;
import "simplebar/dist/simplebar.min.css";

import ResizeObserver from "resize-observer-polyfill";
window.ResizeObserver = ResizeObserver;

// SweetAlert
import Swal from "sweetalert2";
window.Swal = Swal;

import './lib/alpine';
