/*========================================
  Liquid Glass Framework — Main Entry
  Imports and initializes all core JavaScript modules
=========================================*/

import * as Helpers from "./_helpers.js";
import * as Theme from "./_theme.js";
import * as Animations from "./_animations.js";

/**
 * Initialize all framework features
 */
const LiquidGlass = {
  init() {
    Theme.initTheme();
    Theme.setupThemeToggles();
    Animations.initAnimations();
    console.log("%cLiquid Glass Framework Initialized", "color:#007aff;font-weight:bold;");
  },
  ...Helpers,
  ...Theme,
  ...Animations,
};

// Auto-init once DOM is loaded
document.addEventListener("DOMContentLoaded", () => LiquidGlass.init());

// Expose globally for developers
window.LiquidGlass = LiquidGlass;

export default LiquidGlass;