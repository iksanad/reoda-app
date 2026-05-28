/*========================================
  Liquid Glass Framework — JS Helpers
  Core utility functions for DOM interactions and animations
=========================================*/

/**
 * Shorthand DOM selector
 * @param {string} selector
 * @param {boolean} all — optional: select all elements
 */
export const $ = (selector, all = false) =>
  all ? document.querySelectorAll(selector) : document.querySelector(selector);

/**
 * Toggle class on an element
 * @param {HTMLElement} el
 * @param {string} className
 */
export const toggleClass = (el, className) => {
  if (el) el.classList.toggle(className);
};

/**
 * Add a class with a timeout (useful for animations)
 * @param {HTMLElement} el
 * @param {string} className
 * @param {number} duration — milliseconds
 */
export const flashClass = (el, className, duration = 1000) => {
  if (!el) return;
  el.classList.add(className);
  setTimeout(() => el.classList.remove(className), duration);
};

/**
 * Smooth scroll to target
 * @param {string} target
 */
export const smoothScrollTo = (target) => {
  const el = $(target);
  if (el) {
    window.scrollTo({
      top: el.offsetTop,
      behavior: "smooth",
    });
  }
};

/**
 * Throttle function (optimize scroll/resize listeners)
 * @param {Function} func
 * @param {number} limit
 */
export const throttle = (func, limit = 200) => {
  let inThrottle;
  return function () {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
};