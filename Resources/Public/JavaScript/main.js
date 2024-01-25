(()=>{"use strict";function t(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},e(t)}function n(t){var n=function(t,n){if("object"!=e(t)||!t)return t;var o=t[Symbol.toPrimitive];if(void 0!==o){var i=o.call(t,n||"default");if("object"!=e(i))return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(t,"string");return"symbol"==e(n)?n:String(n)}function o(t,e){for(var o=0;o<e.length;o++){var i=e[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(t,n(i.key),i)}}function i(t,e,n){return e&&o(t.prototype,e),n&&o(t,n),Object.defineProperty(t,"prototype",{writable:!1}),t}function r(t,n){if(n&&("object"===e(n)||"function"==typeof n))return n;if(void 0!==n)throw new TypeError("Derived constructors may only return object or undefined");return function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t)}function u(t){return u=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(t){return t.__proto__||Object.getPrototypeOf(t)},u(t)}function l(t,e){return l=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(t,e){return t.__proto__=e,t},l(t,e)}function s(){try{var t=!Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){})))}catch(t){}return(s=function(){return!!t})()}function a(t){var e="function"==typeof Map?new Map:void 0;return a=function(t){if(null===t||!function(t){try{return-1!==Function.toString.call(t).indexOf("[native code]")}catch(e){return"function"==typeof t}}(t))return t;if("function"!=typeof t)throw new TypeError("Super expression must either be null or a function");if(void 0!==e){if(e.has(t))return e.get(t);e.set(t,n)}function n(){return function(t,e,n){if(s())return Reflect.construct.apply(null,arguments);var o=[null];o.push.apply(o,e);var i=new(t.bind.apply(t,o));return n&&l(i,n.prototype),i}(t,arguments,u(this).constructor)}return n.prototype=Object.create(t.prototype,{constructor:{value:n,enumerable:!1,writable:!0,configurable:!0}}),l(n,t)},a(t)}function c(t,e,n){return e=u(e),r(t,h()?Reflect.construct(e,n||[],u(t).constructor):e.apply(t,n))}function h(){try{var t=!Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){})))}catch(t){}return(h=function(){return!!t})()}var f=function(e){function n(){return t(this,n),c(this,n,arguments)}return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),Object.defineProperty(t,"prototype",{writable:!1}),e&&l(t,e)}(n,e),i(n,null,[{key:"create",value:function(t){return new n('The element "'.concat(t,'" does not exist.'))}}]),n}(a(DOMException));function d(t,e,o){return(e=n(e))in t?Object.defineProperty(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}):t[e]=o,t}var p=function(t){return t.solution=".solution",t.solutionContainer=".solution-container",t.solutionCurrentChoice=".solution-current-choice",t.solutionListItem=".solution-list-item",t.solutionLoaderCount=".solution-loader-count",t.solutionMaxChoices=".solution-max-choices",t.solutionModel=".solution-model",t.solutionPrompt=".solution-prompt > pre",t.solutionSelector=".solution-selector",t}({}),m=function(t){return t.solutionCaretVisible="solution-caret-visible",t.solutionProvided="solution-provided",t.solutionStreaming="solution-streaming",t}({}),v=function(t){return t.solutionDelta="solutionDelta",t.solutionError="solutionError",t.solutionFinished="solutionFinished",t}({}),y=function(){function e(n,o,i){t(this,e),d(this,"eventSource",null),d(this,"caretInterval",null),this.solution=n,this.exceptionId=o,this.streamHash=i,this.solutionContainer=this.solution.element.querySelector(p.solutionContainer),this.solutionModel=this.solution.element.querySelector(p.solutionModel),this.solutionMaxChoices=this.solution.element.querySelector(p.solutionMaxChoices),this.solutionPrompt=this.solution.element.querySelector(p.solutionPrompt),this.solutionLoaderCount=this.solution.element.querySelector(p.solutionLoaderCount)}return i(e,[{key:"start",value:function(){if(null===this.eventSource||this.eventSource.CLOSED){var t=new URL(window.location.href);t.pathname="/tx_solver/solution",t.searchParams.set("exception",this.exceptionId),t.searchParams.set("hash",this.streamHash),this.eventSource=new EventSource(t.toString()),this.solution.element.classList.add(m.solutionStreaming),this.caretInterval=setInterval(this.toggleCaret.bind(this),750),this.eventSource.addEventListener(v.solutionDelta,this.handleSolutionDelta.bind(this)),this.eventSource.addEventListener(v.solutionError,this.handleSolutionError.bind(this)),this.eventSource.addEventListener(v.solutionFinished,this.handleSolutionFinished.bind(this))}}},{key:"handleSolutionDelta",value:function(t){var e=JSON.parse(t.data),n=e.data,o=n.model,i=n.numberOfChoices,r=n.numberOfPendingChoices,u=n.prompt;this.solutionContainer.innerHTML=e.content,this.solutionModel.innerHTML=o,this.solutionMaxChoices.innerHTML=i.toString(),this.solutionPrompt.innerHTML=u,r>1&&(this.solutionLoaderCount.innerHTML=r.toString())}},{key:"handleSolutionError",value:function(t){var e=JSON.parse(t.data);this.solution.element.outerHTML=e.content}},{key:"handleSolutionFinished",value:function(){var t;this.solution.element.classList.remove(m.solutionStreaming),this.solution.element.classList.remove(m.solutionCaretVisible),this.solution.element.classList.add(m.solutionProvided),null!==this.caretInterval&&clearInterval(this.caretInterval),null===(t=this.eventSource)||void 0===t||t.close(),this.eventSource=null,this.solution.handleSolutionSelection()}},{key:"toggleCaret",value:function(){this.solution.element.classList.contains(m.solutionCaretVisible)?this.solution.element.classList.remove(m.solutionCaretVisible):this.solution.element.classList.add(m.solutionCaretVisible)}}]),e}(),b=function(){function e(n){t(this,e),this.element=n}return i(e,[{key:"canBeStreamed",value:function(){return"exceptionId"in this.element.dataset&&"streamHash"in this.element.dataset&&!this.element.classList.contains(m.solutionProvided)}},{key:"createStream",value:function(){if(!this.canBeStreamed())return null;var t=this.element.dataset.exceptionId,e=this.element.dataset.streamHash;return new y(this,t,e)}},{key:"handleSolutionSelection",value:function(){var t=this.element.querySelectorAll(p.solutionListItem),e=this.element.querySelector(p.solutionCurrentChoice);t.forEach((function(n){var o=parseInt(n.dataset.solutionChoiceIndex),i=n.querySelector(p.solutionSelector);null==i||i.addEventListener("input",(function(){null!==e&&(e.innerHTML=(o+1).toString(),n.setAttribute("aria-hidden","false"),t.forEach((function(t){t!==n&&t.setAttribute("aria-hidden","true")})))}))}))}}],[{key:"create",value:function(){var t=document.querySelector(p.solution);if(!(t instanceof HTMLElement))throw f.create(p.solution);return new e(t)}}]),e}();try{var S=b.create();S.canBeStreamed()?S.createStream().start():S.handleSolutionSelection()}catch(t){}})();