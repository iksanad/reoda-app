/*========================================
  Liquid Glass Framework — Theme Manager
  Handles theme toggling, accent colors, and persistence
=========================================*/

import { $, toggleClass } from "./_helpers.js";

/**
 * Apply stored or default theme settings
 */
export const initTheme = () => {
  const savedTheme = localStorage.getItem("lg-theme") || "light";
  const savedAccent = localStorage.getItem("lg-accent") || "blue";
  const savedGlass = localStorage.getItem("lg-glass") || "medium";

  document.documentElement.setAttribute("data-theme", savedTheme);
  document.documentElement.setAttribute("data-accent", savedAccent);
  document.documentElement.setAttribute("data-glass", savedGlass);
};

/**
 * Toggle between light and dark themes
 */
export const toggleTheme = () => {
  const current = document.documentElement.getAttribute("data-theme");
  const next = current === "dark" ? "light" : "dark";
  document.documentElement.setAttribute("data-theme", next);
  localStorage.setItem("lg-theme", next);
};

/**
 * Set a new accent color
 * @param {string} accent - e.g. 'green', 'pink', 'orange'
 */
export const setAccent = (accent) => {
  document.documentElement.setAttribute("data-accent", accent);
  localStorage.setItem("lg-accent", accent);
};

/**
 * Adjust glass intensity
 * @param {string} level - 'soft', 'medium', 'strong'
 */
export const setGlassLevel = (level) => {
  document.documentElement.setAttribute("data-glass", level);
  localStorage.setItem("lg-glass", level);
};

/**
 * Initialize event listeners for theme toggles
 */
export const setupThemeToggles = () => {
  const themeToggle = $("[data-toggle-theme]");
  const accentButtons = $("[data-set-accent]", true);

  if (themeToggle) {
    themeToggle.addEventListener("click", toggleTheme);
  }

  accentButtons.forEach((btn) => {
    btn.addEventListener("click", () => setAccent(btn.dataset.setAccent));
  });
};

// Initialize automatically
document.addEventListener("DOMContentLoaded", () => {
  initTheme();
  setupThemeToggles();
});