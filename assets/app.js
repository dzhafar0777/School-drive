document.addEventListener('DOMContentLoaded',()=>{
 const io=new IntersectionObserver(es=>es.forEach(x=>{if(x.isIntersecting){x.target.classList.add('in');io.unobserve(x.target)}}),{threshold:.12});
 document.querySelectorAll('.card,.hero>*').forEach((el,i)=>{el.classList.add('rv');el.style.transitionDelay=(i%6)*70+'ms';io.observe(el)});
 document.querySelectorAll('.card').forEach(c=>c.addEventListener('mousemove',e=>{const r=c.getBoundingClientRect();c.style.setProperty('--mx',e.clientX-r.left+'px');c.style.setProperty('--my',e.clientY-r.top+'px')}));
 document.querySelectorAll('[data-count]').forEach(el=>{const t=+el.dataset.count;let s=null;const f=ts=>{s=s||ts;const p=Math.min((ts-s)/1200,1);el.textContent=Math.round(t*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(f)};requestAnimationFrame(f)});
 setTimeout(()=>document.querySelectorAll('.ring').forEach(r=>r.classList.add('on')),300);
});
