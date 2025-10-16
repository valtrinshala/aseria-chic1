window.queuedStrings={receipt:[]};let m={printing:!1,order:null},k={},M={};const I=localStorage.getItem("auto_print_orders");if(I!=null)try{k=JSON.parse(I)}catch{localStorage.removeItem("auto_print_orders")}window.orderPagMan={parentEl:".card-pagination",placementElement:".card-pagination .pag-numbers",activeClass:"active",reachableClass:"reachable",clickSelect:"[page]",elementHTML:'<button class="pag-btn number {active_class} {reachable_class}" page="{number}">{number}</button>',current:1,reachableDistance:5,pagAmount:0,maxPags:5,currPagAmount:0,orderElements:{},setup:function(){const e=document.querySelector(this.parentEl+" .previous"),t=document.querySelector(this.parentEl+" .next");e.addEventListener("click",r=>{window.orderPagMan.current-1<1||(window.orderPagMan.clickPag(--window.orderPagMan.current),window.orderPagMan.orderReset())}),t.addEventListener("click",r=>{window.orderPagMan.current>Object.keys(window.orderPagMan.orderElements).length-1||(window.orderPagMan.clickPag(++window.orderPagMan.current),window.orderPagMan.orderReset())})},addPag:function(e=null){if(this.pagAmount>=this.maxPags)return;let t=this.currPagAmount+1;if(this.currPagAmount++,this.currPagAmount<this.current-this.maxPags*.5)return;this.orderElements[t]=e;const r=document.querySelector(this.placementElement);if(!r)return;let o=this.elementHTML;o=o.replaceAll("{number}",t);let n="",d="";t==this.current?n=this.activeClass:t<this.current+this.reachableDistance&&t>this.current-this.reachableDistance&&(d=this.reachableClass),o=o.replaceAll("{active_class}",n),o=o.replaceAll("{reachable_class}",d),r.innerHTML+=o;const c=document.querySelectorAll(this.placementElement+" "+this.clickSelect);for(let u of c)u.addEventListener("click",a=>{const s=u.getAttribute("page");orderPagMan.clickPag(s)});this.pagAmount++},clearPags:function(){this.currPagAmount=0,this.pagAmount=0;const e=document.querySelector(this.placementElement);e&&(e.innerHTML="")},clickPag:function(e){this.current=e;const t=orderPagMan.orderElements[this.current];t&&t.scrollIntoView({behavior:"smooth"}),"orderReset"in window.orderPagMan&&window.orderPagMan.orderReset()}};const _={clearOrders:function(){const e=document.querySelectorAll(".order-card[order]");if(e.length>0)for(let t=e.length-1;t>=0;t--)e[t].remove(0)},addOrder:function(e){const t=document.getElementById("ordering");let r=e.status.replaceAll(" ","_");const o=document.createElement("div");o.classList.add("order-card"),o.classList.add("holder"),o.classList.add("w-fit-content"),o.classList.add("mt-3"),o.classList.add("flex-shrink-0"),o.classList.add(r),o.setAttribute("order",e.id),o.setAttribute("cancel_amount",e.cancel_amount);let n="";e.note!=""&&(n=`
                <div class="Note mb-4 p-4">
                    <div class="title fw-bold">${window.keys.note}:</div>
                    <div class="note">${e.note}</div>
                </div>
            `);const d=t.getAttribute("buttons");let c="",u="";if(d){const i=d.split(",");for(let y of i){const l=y.split("_");let w=l[0],p="";if(l.length>1&&l[1]=="disabled"&&(p="disabled"),w=="edit"&&(c+=`
                        <a href="${window.routes.pos_index}?order_id=${e.id}" ${p} class="px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.editOrder}
                        </a>
                    `),w=="print"&&(u+=`
                        <button ${p} class="px-1 py-3 print-order col btn btn-primary d-flex align-items-center gap-3 justify-content-center w-100">
                            ${window.keys.printOrder}
                        </button>
                    `),w=="cancel"){let g="max-w-35",b="w-80";i.includes("edit")||(g="max-w-100",b="w-100"),c+=`
                        <button ${p} class="px-1 py-2 coming-soon refund-order col btn text-danger btn-primary btn-secondary-cus d-flex align-items-center gap-3 justify-content-center ${b} ${g}">
                            ${window.keys.cancelOrder}
                        </button>
                    `}if(w=="refund"){let g=window.keys.refundOrder;l.length>2&&l[2]=="fake"&&(g=window.keys.cancelOrder);let b="max-w-35",v="w-80";i.includes("edit")||(b="max-w-100",v="w-100"),c+=`
                        <button ${p} class="px-1 py-2 cancel-order col btn text-danger btn-primary btn-secondary-cus d-flex align-items-center gap-3 justify-content-center ${v} ${b}">
                            ${g}
                            <!-- ${window.keys.refundOrder} -->
                        </button>
                    `}if(w=="ack"){let g=e.que_ready?"d-none":"",b=e.que_ready?"":"d-none";u+=`
                        <button ${p} class="${g} ack-ready-button px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.readybtn}
                        </button>
                    `,u+=`
                        <button ${p} class="${b} ack-button px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.acknowledge}
                        </button>
                    `}w=="payment"&&(c+=`
                        <a ${p} href="${window.routes.pos_index}?order_id=${e.id}" class="px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.payment}
                        </a>
                    `)}}let a="",s=e.items,f=-1;for(let i of s){f++;let y=null,l=null;f+1<s.length&&(y=s[f+1]),f-1>=0&&(l=s[f-1]);const w=!i.isMeal&&l!=null&&l.isMeal?"prod-after-meal":"",p=i.mealName||i.isMeal&&y!=null&&!y.isMeal?"no-border":"",g=i.isMeal?"meal":"",b=i.checked||"meal"in i&&i.meal.checked?"crossed":"",v="meal"in i?i.meal.id:i.id;let E="";if(!i.isMeal){let h="";i.checked&&(h="checked"),E=`
                    <div class="form-check form-check-sm form-check-custom form-check-solid align-items-start">
                        <input together="${v}" ${h} class="p-4 form-check-input item-completable" type="checkbox" item-id="${i.id}" order-id="${e.id}" key="${i.randomKey}">
                    </div>
                `}let S="";if("extra"in i&&i.extra.length!=0){S=`
                    <div class="extra">
                        <div class="title crossable fw-bold">${window.keys.extraAdds}:</div>
                        <div class="extra-items mb-2">
                `;for(let h of i.extra)S+=`<div class="extra-item crossable ps-3">${h.quantity}x ${h.name}</div>`;S+="</div></div>"}let B="";if("removed"in i&&i.removed.length!=0){B=`
                    <div class="no">
                        <div class="title crossable fw-bold">${window.keys.extraRemoved}:</div>
                        <div class="no-items">
                `;for(let h of i.removed)B+=`<div class="extra-item crossable ps-3">${h.name}</div>`;B+="</div></div>"}let K=`
                ${S}
                ${B}
            `,R="";i.isMeal||i.mealName,a+=`
                <div class="d-flex ${w} ${p} justify-content-between ${g} food-item ${b}" order="${e.id}" together="${v}" item="${i.id}" randomKey="${i.randomKey}">
                    <div class="left d-flex gap-4">
                        ${E}

                        <div class="">
                            <div class="item">
                                <span class="crossable title fw-bold fs-4">${i.quantity}x ${i.name}</span>
                            </div>
                            ${K}
                        </div>
                    </div>

                    <div class="right">
                        ${R}
                    </div>
                </div>
            `}o.innerHTML=`
            <div class="card ms-3 p-0">
                <div class="d-flex justify-content-between px-3 align-items-center py-1 border1 status-color rounded-top" style="background-color: ${e.status_color}">

                    <h3 class="card-title searchable-text fw-bold m-0 text-white">#${e.view_id}</h3>
                    <h3 class="card-title searchable-text fw-bold m-0 status-text text-white">${e.status}</h3>
                    <div class="card-toolbar">
                        <span class="searchable-text text-white">${e.view_price}</span>
                    </div>

                </div>
                <div class="card-body p-0 rounded-bottom">
                    <div class="px-5 bg-body position-relative pt-4">
                        <div class="row">
                            <div class="col">
                                <div class="fs-5 fw-bold searchable-text">${e.table}</div>
                                <div class="fs-6 fw-medium searchable-text">${window.keys.assignment}: <span class="chef-assign">${e.chef}</span></div>
                            </div>
                            <div class="col text-end">
                                <div class="fs-5 text-danger fw-bold time-lapse">0s</div>
                                <div class="fs-6 date-stamp fw-medium" stamp="${e.time}">${window.keys.ordered}: ${e.view_date}</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 bg-body position-relative pt-4">
                        <div class="row">
                            <div class="col">
                                <div class="fs-6 fw-medium"><span class="completed-items">${e.completed}</span>/<span items="${e.count}" class="order-count">${e.count}</span> ${window.keys.com_orders}</div>
                            </div>
                            <div class="col text-end">
                                <div class="fs-6 fw-medium"><span class="progress-percentage">${e.progress}</span>%</div>

                            </div>
                        </div>
                    </div>
                    <div class="bg-body pb-4 px-5">
                        <div class="progress h-5px w-100">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${e.progress}%" aria-valuenow="${e.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="px-5 bg-body d-flex gap-2">
                        ${c}
                    </div>

                    <div class="px-5 bg-body d-flex gap-2 pt-2">
                        ${u}
                    </div>

                    <div class="card-bottom-bit bg-body pt-4 px-5 overflow-auto rounded-bottom">
                        <div class="food-items mb-4">
                            ${a}
                        </div>

                        ${n}
                    </div>

                </div>
            </div>
        </div>
        `,t.appendChild(o),orderPagMan.addPag(o)}};$(document).on("click",".item-completable",function(){const e=this.getAttribute("item-id"),t=this.getAttribute("order-id"),r=this.getAttribute("key"),o=this.getAttribute("together"),n=this;if(!e||!t||!r)return;let d="true";n.checked||(d="false");let c=n.checked?"add":"remove";n.checked;let u=document.querySelectorAll(`[order="${t}"][together="${o}"][randomKey="${r}"]`);for(let a of u)a.classList[c]("crossed");x(t),$.ajax({url:"/admin/prepare/item",method:"POST",async:!0,dataType:"json",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:{order_id:t,item_id:e,added:d,randomKey:r},success:a=>{if(a.status==2){location.href=a.uri;return}if(a.status!=0){n.checked=!d;let f=document.querySelectorAll(`[order="${t}"][together="${o}"][randomKey="${r}"]`);for(let i of f)i.classList[c]("crossed");"message"in a&&a.message!=""&&Swal.fire({text:a.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}x(t)==100?O(t,2):O(t,1),"message"in a&&a.message!=""&&Swal.fire({text:a.message,icon:"success",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn btn-primary"}})},error:a=>{n.checked=!d;let s=document.querySelectorAll(`[order="${t}"][together="${o}"][randomKey="${r}"]`);for(let f of s)f.classList[c]("crossed");console.error(a),Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})});$(document).on("click",".order-assign",function(){const e=this.getAttribute("order-id");e&&$.ajax({url:"/admin/assignToMe",method:"POST",async:!0,dataType:"json",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:{order_id:e},success:t=>{if(t.status==2){location.href=t.uri;return}if(t.status!=0){Swal.fire({text:t.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}this.textContent=window.keys.confirmBtn,this.classList.remove("order-assign"),this.classList.add("order-confirm"),O(e,1),D(e,{name:document.getElementById("current-user").getAttribute("user")})},error:t=>{console.error(t),Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})});$(document).on("click",".order-confirm",function(){const e=this.getAttribute("order-id");$.ajax({url:"/admin/confirmOrder",method:"POST",async:!0,dataType:"json",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:{order_id:e},success:t=>{if(t.status==2){location.href=t.uri;return}if(t.status!=0){Swal.fire({text:t.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}const r=document.querySelector(`.order-card[order="${e}"]`);r&&r.remove()},error:t=>{console.error(t),Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})});window.cancelation={current:null,orders:[],reasons:{},keySwaps:{},update:function(){return this.current!=null||this.orders.length==0?!1:(this.current=this.orders.shift(),this.current)},acknowledge:function(){let e=this.current;$.ajax({method:"POST",url:"/admin/approveCancelKitchen",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:{order_id:e},success:t=>{if(t.status==2){location.href=t.uri;return}if(t.status==1){Swal.fire({text:t.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}t.status==0&&(this.current=null,$("#ack-modal").modal("hide")),t.message!=""&&Swal.fire({text:data.message,icon:"success",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn btn-primary"}})},error:t=>{Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})},getId:function(e){return e in this.keySwaps?this.keySwaps[e]:""},getReason:function(e){return e in this.reasons?this.reasons[e]:""}};$(document).on("click",".acknowledge-current-order",function(){window.cancelation.acknowledge()});$(document).on("click",".coming-soon",function(){Swal.fire({text:window.keys.comingSoon,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})});$(document).ready(()=>{window.orderPagMan.setup(),setInterval(()=>{(window.cancelation.orders.length!=0||window.cancelation.current!=null)&&$("#ack-modal").modal("show");let e=window.cancelation.update();e!=!1&&($("#ack-modal .order-id").text(window.cancelation.getId(e)),$("#ack-modal .order-reason").text(window.cancelation.getReason(e)),$("#ack-modal").modal("show"))},50)});function O(e,t){const r=[window.keys.waitingOrder,window.keys.progressOrder,window.keys.completedOrder],o=["#E7B951","#FF3636","#29B93A"],n=document.querySelector(`.order-card[order="${e}"]`);if(!n)return!1;for(let d of r){let c=d.replaceAll(" ","_");n.classList.remove(c)}if(t in r){let d=r[t];const c=d.replaceAll(" ","_");n.classList.add(c);const u=n.querySelector(".status-color");u&&(u.style.backgroundColor=o[t]);const a=n.querySelector(".status-text");return a&&(a.textContent=d),!0}return!1}function D(e,t){const r=document.querySelector(`.order-card[order="${e}"]`);if(!r)return!1;const o=r.querySelector(".chef-assign");return o&&"name"in t&&(o.textContent=t.name),!0}function x(e){const t=document.querySelector(`.order-card[order="${e}"]`);if(!t)return 0;const r=t.querySelectorAll("input[item-id]"),o=r.length;let n=0;r.forEach(s=>{s.checked&&n++});const d=(n/o*100).toFixed(2),c=t.querySelector(".progress-bar");c&&(c.style.width=`${d}%`);const u=t.querySelector(".completed-items");u&&(u.textContent=n);const a=t.querySelector(".progress-percentage");return a&&(a.textContent=d),d}const j=1,P=j*60,T=P*60;function A(){const e=Date.now()/1e3,t=document.querySelectorAll(".order-card[order]");for(let r of t){const o=r.querySelector(".date-stamp");if(!o)return;const n=r.querySelector(".time-lapse");if(!n)return;const d=o.getAttribute("stamp"),c=e-parseFloat(d),u=Math.floor(c/T),a=Math.floor(c%T/P),s=Math.floor(c%P/j),f=[];u!=0&&f.push(`${u}h`),(a!=0||u!=0)&&f.push(`${a}m`),f.push(`${s}s`);let i=f.join(" ");n.textContent=i}}setInterval(()=>{A()},1e3);function C(){const e=document.getElementById("ordering");let t="/admin/pos/kitchen/orders";e&&e.getAttribute("type")=="ready"&&(t="/admin/pos/readyOrders"),e&&e.getAttribute("type")=="e-kiosk"&&(t="/admin/pos/eKioskOrders");let r=[];const o=Object.keys(k);if(o.length>0){let n=1;for(let d of o){const c="uuid"+n+"="+d;r.push(c),n++}}$.ajax({url:t+"?"+r.join("&"),method:"GET",async:!0,dataType:"json",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:n=>{if(n.status==2){location.href=n.uri;return}if(n.status!=0){"message"in n&&n.message!=""&&Swal.fire({text:n.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}_.clearOrders();let d=n.data.canceled_orders;for(let s of d)window.cancelation.orders.push(s.id),window.cancelation.reasons[s.id]=s.cancellation_reason,window.cancelation.keySwaps[s.id]=s.order_number;if(k={},localStorage.removeItem("auto_print_orders"),"auto_print_orders"in n.data)for(let s of n.data.auto_print_orders)s.id in M||(M[s.id]=s,N(s.printString,"auto-"+s.id));orderPagMan.clearPags();let c=n.data.orders;const u=document.getElementsByClassName("no-orders-pop");if(c.length==0)for(let s of u)s.classList.remove("d-none");else for(let s of u)s.classList.add("d-none");for(let s of c)_.addOrder(s);A();const a=document.querySelectorAll(".order-card[order]");for(let s of a){const f=s.getAttribute("order");x(f)}window.orderPagMan.orderReset=function(){_.clearOrders();let s=n.data.canceled_orders;for(let l of s)window.cancelation.orders.push(l.id),window.cancelation.reasons[l.id]=l.cancellation_reason,window.cancelation.keySwaps[l.id]=l.order_number;orderPagMan.clearPags();let f=n.data.orders;const i=document.getElementsByClassName("no-orders-pop");if(f.length==0)for(let l of i)l.classList.remove("d-none");else for(let l of i)l.classList.add("d-none");for(let l of f)_.addOrder(l);A();const y=document.querySelectorAll(".order-card[order]");for(let l of y){const w=l.getAttribute("order");x(w)}},"message"in n&&n.message!=""&&Swal.fire({text:n.message,icon:"success",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn btn-primary"}})},error:n=>{Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})}window.debug_print=!1;window.debug_print&&(document.addEventListener("click",e=>{console.log(e.button),e.button==0&&C()}),document.addEventListener("contextmenu",e=>(e.preventDefault(),e.stopPropagation(),console.log(m),!1)));window.debug_print||(C(),setInterval(()=>{C()},1e4));document.addEventListener("DOMContentLoaded",()=>{window.debug_print&&(window.Mine={postMessage:r=>alert(r)});let e=localStorage.getItem("printer_settings");if(e!=null)try{if(e=JSON.parse(e),"receipt_printer"in e&&e.receipt_printer&&e.receipt_printer.status==1&&"Mine"in window){const r=e.receipt_printer.printer.type;let o="order";r=="sticker_printer"&&(o="sticker"),window.Mine.postMessage(`con:${o}-${e.receipt_printer.printer.name}:${e.receipt_printer.printer.ip}:${e.receipt_printer.printer.port}`)}}catch{}const t=document.querySelectorAll(".order-card[order]");for(let r of t){const o=r.getAttribute("order");x(o)}});$(document).on("click",".print-order",function(){const e=this.closest("[order]");if(e){const t=e.getAttribute("order");$.ajax({method:"GET",url:"/admin/pos/printPosOrder?order_id="+t,dataType:"json",success:r=>{if(r.status==2){location.href=r.uri;return}if(r.status==1){Swal.fire({text:r.message,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}const o=r.data;if(!("string"in o)){Swal.fire({text:window.keys.printerOrderDoesNotExist,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}});return}N(o.string,t),r.message!=""&&Swal.fire({text:r.message,icon:"success",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn btn-primary"}})},error:r=>{console.error(r),Swal.fire({text:window.keys.unexpectedError,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})}})}});window.failed_connection=(e,t,r)=>{if(m.printing=!1,t=="Succeed"){if(window.debug_print&&alert(e+" printed"),m.order!=null&&m.order!=null&&m.order.startsWith("auto-")){const n=m.order.split("auto-")[1];k[n]=n,localStorage.setItem("auto_print_orders",JSON.stringify(k))}return}Swal.fire({text:e+" "+window.keys.connectionFailReason+": "+t,icon:"error",buttonsStyling:!1,confirmButtonText:window.keys.confirmButtonOk,customClass:{confirmButton:"btn fw-bold btn-primary"}})};setInterval(()=>{"Mine"in window&&window.queuedStrings.receipt.length!=0&&window.queueInterval==null&&q()},1e3);let L=Date.now();function N(e,t=null){if(Date.now()-L>3e3&&window.queuedStrings.receipt.length==0){if(m.printing){window.queuedStrings.receipt.push({string:e,nonce:t}),q();return}L=Date.now(),m.printing=!0,m.order=t,invoicePrinting(e);return}window.queuedStrings.receipt.push({string:e,nonce:t}),window.queueInterval==null&&q()}function q(){window.queueInterval!=null&&clearInterval(window.queueInterval),window.queueInterval=setInterval(()=>{if(m.printing)return;if(window.queuedStrings.receipt.length==0){window.queueInterval!=null&&clearInterval(window.queueInterval),window.queueInterval=null;return}let e="";window.debug_print&&alert(window.currentPrinting);let t=window.queuedStrings.receipt.shift();if(e=t.string,!(!e||e=="")&&(window.debug_print&&alert("Printing "+window.currentPrinting+" "+e),m.printing=!0,m.order=t.nonce,invoicePrinting(e),window.queuedStrings.receipt.length==0)){window.queueInterval!=null&&clearInterval(window.queueInterval),window.queueInterval=null;return}},3e3)}
