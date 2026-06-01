(function(){
    function initRatingContainers(){
        document.querySelectorAll('.rating-container').forEach(function(container){
            var inputs = Array.from(container.querySelectorAll('input.single-room-rating'));
            if(!inputs.length) return;

            // ensure only one input has tabindex=0 (focusable by Tab)
            function setTabindex(index){
            inputs.forEach(function(inp,i){
                inp.tabIndex = (i === index) ? 0 : -1;
            });
            }

            // prefer checked input, otherwise make the visually-first star tabbable.
            // DOM order is reversed (5..1) while CSS uses row-reverse, so visually-first is the last DOM element.
            var checkedIndex = inputs.findIndex(function(i){ return i.checked; });
            var defaultIndex = (checkedIndex >= 0) ? checkedIndex : (inputs.length - 1);
            setTabindex(defaultIndex);

            // change handler: update tabbable to changed input
            inputs.forEach(function(inp, idx){
            inp.addEventListener('change', function(){
                setTabindex(idx);
                inp.focus();
            });

            inp.addEventListener('focus', function(){
                setTabindex(idx);
            });
            });

            // keyboard navigation within the group (keeps visual order)
            container.addEventListener('keydown', function(e){
            var active = document.activeElement;
            var idx = inputs.indexOf(active);
            if(idx === -1) return;

            if(e.key === 'ArrowLeft' || e.key === 'ArrowDown'){
                e.preventDefault();
                var next = Math.min(inputs.length - 1, idx + 1);
                inputs[next].checked = true;
                setTabindex(next);
                inputs[next].focus();
            } else if(e.key === 'ArrowRight' || e.key === 'ArrowUp'){
                e.preventDefault();
                var prev = Math.max(0, idx - 1);
                inputs[prev].checked = true;
                setTabindex(prev);
                inputs[prev].focus();
            }
            });

            // if focus enters the container (e.g. via tabbing to label), focus the checked or visually-first star
            container.addEventListener('focusin', function(e){
            var active = document.activeElement;
            if(active && active.classList && active.classList.contains('single-room-rating')) return;
            var current = inputs.findIndex(function(i){ return i.checked; });
            var focusIndex = current >= 0 ? current : (inputs.length - 1);
            inputs[focusIndex].focus();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRatingContainers);
    } else {
        initRatingContainers();
    }
})();