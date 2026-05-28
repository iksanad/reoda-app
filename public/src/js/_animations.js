/*========================================
  Liquid Glass Framework — Animations
  Handles scroll-based reveals, parallax, and motion effects
=========================================*/

import { $, throttle } from "./_helpers.js";

/**
 * Fade-in on scroll using IntersectionObserver
 */
export const initScrollAnimations = () => {
  const elements = $("[data-animate]", true);

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.2 }
  );

  elements.forEach((el) => observer.observe(el));
};

/**
 * Parallax scroll effect for backgrounds or images
 */
export const initParallax = () => {
  const parallaxEls = $("[data-parallax]", true);

  const handleScroll = throttle(() => {
    const scrollY = window.scrollY;
    parallaxEls.forEach((el) => {
      const speed = parseFloat(el.dataset.parallax) || 0.3;
      el.style.transform = `translateY(${scrollY * speed}px)`;
    });
  }, 20);

  window.addEventListener("scroll", handleScroll);
};

/**
 * Floating blur glow effect on hover (for cards/buttons)
 */
export const initHoverGlows = () => {
  const glowTargets = $("[data-glow]", true);

  glowTargets.forEach((el) => {
    el.addEventListener("mousemove", (e) => {
      const rect = el.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      el.style.background = `
        radial-gradient(circle at ${x}px ${y}px,
        rgba(255,255,255,0.15), transparent 70%)`;
    });

    el.addEventListener("mouseleave", () => {
      el.style.background = "";
    });
  });
};

/**
 * Initialize all interactive motion effects
 */
export const initAnimations = () => {
  initScrollAnimations();
  initParallax();
  initHoverGlows();
};

// Auto-init after DOM is ready
document.addEventListener("DOMContentLoaded", initAnimations);