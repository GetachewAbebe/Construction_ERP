import{c}from"./sun-DhUZgNFJ.js";import{r as l,j as o}from"./app-DTmE7Tv8.js";import{j as v,k as w,l as x}from"./AuthenticatedLayout-B5PNa7WZ.js";/**
 * @license lucide-react v1.47.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const h={name:"archive",size:24,node:[["rect",{width:"20",height:"5",x:"2",y:"3",rx:"1",key:"1wp1u1"}],["path",{d:"M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8",key:"1s80jp"}],["path",{d:"M10 12h4",key:"a56b0p"}]]};h.node;const E=c(h);/**
 * @license lucide-react v1.47.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const f={name:"folder-kanban",size:24,node:[["path",{d:"M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z",key:"1fr9dc"}],["path",{d:"M8 10v4",key:"tgpxqk"}],["path",{d:"M12 10v2",key:"hh53o1"}],["path",{d:"M16 10v6",key:"1d6xys"}]]};f.node;const A=c(f);/**
 * @license lucide-react v1.47.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const m={name:"layers",size:24,node:[["path",{d:"M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z",key:"zw3jo"}],["path",{d:"M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12",key:"1wduqc"}],["path",{d:"M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17",key:"kqbvx6"}]],aliases:["layers-3"]};m.node;const C=c(m);function L({date:e,preference:t,className:i="",showTooltip:y=!0}){const[r,d]=l.useState(t||v());if(l.useEffect(()=>{if(t){d(t);return}const a=u=>{d(u.detail)};return window.addEventListener("calendar-preference-changed",a),()=>{window.removeEventListener("calendar-preference-changed",a)}},[t]),!e)return o.jsx("span",{className:"text-slate-400",children:"—"});const s=w(e);if(!s)return o.jsx("span",{className:i,children:String(e)});const g=x(e,r);let n;if(typeof e=="string"){const a=e.split("T")[0].split(" ")[0].split("-");a.length===3?n=new Date(parseInt(a[0],10),parseInt(a[1],10)-1,parseInt(a[2],10)):n=new Date(e)}else n=new Date(e);const p=n.toLocaleDateString("en-GB",{day:"2-digit",month:"short",year:"numeric"}),k=r==="ec"?`Gregorian: ${p}`:r==="gc"?`Ethiopian: ${s.formattedAm}`:`GC: ${p} | EC: ${s.formattedAm}`;return o.jsx("span",{className:`inline-flex items-center gap-1 font-mono ${i}`,title:y?k:void 0,children:g})}export{E as A,L as C,A as F,C as L};
