(() => {
 const preference=window.matchMedia('(prefers-reduced-motion: reduce)');
 let observer;
 const selector='section:not(.hero) .section-header, .about-text, .about-image, .why-choose-us > h3, .feature-card, .package-card, .tour-selector, .tour-intro, .day-card, .destination-card, .gallery-item, .review-card, .review-action, .contact-info, .contact-form, footer .footer-col';
 const targets=[...document.querySelectorAll(selector)];
 const reveal=element=>{element.classList.add('is-revealed');observer?.unobserve(element);};
 function setup(){
  observer?.disconnect();
  targets.forEach(element=>{element.classList.remove('scroll-reveal','reveal-pending','is-revealed');element.style.removeProperty('--reveal-delay');});
  if(preference.matches || !('IntersectionObserver' in window))return;
  try {
   observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting)reveal(entry.target);});},{threshold:0,rootMargin:'0px 0px -40px 0px'});
   targets.forEach(element=>{
    element.classList.add('scroll-reveal','reveal-pending');
    const siblings=[...element.parentElement.children].filter(child=>targets.includes(child));
    const columns=getComputedStyle(element.parentElement).gridTemplateColumns.split(' ').filter(value=>value && value!=='none').length || 1;
    const index=siblings.indexOf(element);const delay=Math.min((index%columns)*75,225);
    element.style.setProperty('--reveal-delay',`${delay}ms`);
    if(element.matches('.about-image,.gallery-item'))element.dataset.reveal='image';
    else if(element.matches('.about-text,.contact-info'))element.dataset.reveal='left';
    else if(element.matches('.contact-form'))element.dataset.reveal='right';
    observer.observe(element);
   });
  } catch(error) { observer?.disconnect();targets.forEach(element=>element.classList.remove('reveal-pending')); }
 }
 document.addEventListener('focusin',event=>{let element=event.target;while(element instanceof Element){if(element.classList.contains('reveal-pending'))reveal(element);element=element.parentElement;}});
 preference.addEventListener('change',setup);
 setup();
})();
