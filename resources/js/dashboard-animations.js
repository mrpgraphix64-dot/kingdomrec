// Dashboard animations have been completely disabled.
// GSAP caused page flashes on hard navigations.
// Lenis smooth scrolling intercepted native scroll events,
// which broke scrolling inside the sidebar and main content containers
// (which rely on overflow-y-auto).
// 
// Relying on native CSS transitions and scrolling provides a much 
// smoother and glith-free experience in a traditional Laravel application.

document.addEventListener('DOMContentLoaded', () => {
    // Intentionally empty to prevent FOUC / scroll-jacking bugs.
});
