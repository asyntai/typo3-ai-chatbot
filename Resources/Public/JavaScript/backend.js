(function(){
    var currentState = null;

    function showAlert(msg, ok){
        var el=document.getElementById('asyntai-alert'); if(!el) return;
        el.style.display='block';
        el.className='callout '+(ok?'callout-success alert alert-success':'callout-danger alert alert-danger');
        el.textContent=msg;
    }

    function generateState(){
        return 'typo3_'+Math.random().toString(36).substr(2,9);
    }

    function updateFallbackLink(){
        var fallbackLink = document.getElementById('asyntai-fallback-link');
        if(fallbackLink && currentState){
            fallbackLink.href = 'https://asyntai.com/wp-auth?platform=typo3&state='+encodeURIComponent(currentState);
        }
    }

    function openPopup(){
        currentState = generateState();
        updateFallbackLink();
        var base='https://asyntai.com/wp-auth?platform=typo3';
        var url=base+(base.indexOf('?')>-1?'&':'?')+'state='+encodeURIComponent(currentState);
        var w=800,h=720;var y=window.top.outerHeight/2+window.top.screenY-(h/2);var x=window.top.outerWidth/2+window.top.screenX-(w/2);
        var pop=window.open(url,'asyntai_connect','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='+w+',height='+h+',top='+y+',left='+x);
        
        // Check if popup was blocked after a short delay
        setTimeout(function(){
            if(!pop || pop.closed || typeof pop.closed=='undefined'){ 
                showAlert('Popup blocked. Please allow popups or use the link below.', false); 
                return; 
            }
            pollForConnection(currentState);
        }, 100);
    }

    // Initialize fallback link on page load
    currentState = generateState();
    updateFallbackLink();

    function pollForConnection(state){
        var attempts=0;
        function check(){
            if(attempts++>60) return;
            var script=document.createElement('script');
            var cb='asyntai_cb_'+Date.now();
            script.src='https://asyntai.com/connect-status.js?state='+encodeURIComponent(state)+'&cb='+cb;
            window[cb]=function(data){ 
                try{ delete window[cb]; }catch(e){}
                if(data && data.site_id){ 
                    saveConnection(data); 
                    return; 
                }
                setTimeout(check, 500);
            };
            script.onerror=function(){ 
                setTimeout(check, 1000); 
            };
            document.head.appendChild(script);
        }
        setTimeout(check, 800);
    }

    function saveConnection(data){
        showAlert('Asyntai connected. Saving…', true);
        var payload={ site_id: data.site_id||'' };
        if(data.script_url) payload.script_url=data.script_url;
        if(data.account_email) payload.account_email=data.account_email;
        
        // Post to the same module URL (handled by indexAction)
        var saveUrl = window.location.href;
        
        fetch(saveUrl, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
            credentials:'same-origin',
            body: JSON.stringify(payload)
        }).then(function(r){ 
            if(!r.ok) {
                return r.text().then(function(text) {
                    throw new Error('HTTP '+r.status + ': ' + text);
                });
            }
            return r.json(); 
        }).then(function(json){
            if(!json || !json.success) throw new Error(json && json.error || 'Save failed');
            showAlert('Asyntai connected. Chatbot enabled on all pages.', true);
            var status=document.getElementById('asyntai-status');
            if(status){
                var html='Status: <span style="color:#008a20;">Connected</span>';
                if(payload.account_email){ html+=' as '+payload.account_email; }
                html += ' <button id="asyntai-reset" class="btn btn-default">Reset</button>';
                status.innerHTML=html;
            }
            var box=document.getElementById('asyntai-connected-box'); if(box) box.style.display='block';
            var wrap=document.getElementById('asyntai-popup-wrap'); if(wrap) wrap.style.display='none';
        }).catch(function(err){ 
            showAlert('Could not save settings: '+(err && err.message || err), false); 
        });
    }

    function resetConnection(){
        // Post to the same module URL with reset action (handled by indexAction)
        var resetUrl = window.location.href;
        
        fetch(resetUrl, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
            credentials:'same-origin',
            body: JSON.stringify({action: 'reset'})
        }).then(function(r){ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); }).then(function(){
            window.location.reload();
        }).catch(function(err){ 
            showAlert('Reset failed: '+(err && err.message || err), false); 
        });
    }

    document.addEventListener('click', function(ev){ 
        var t=ev.target; 
        if(t && t.id==='asyntai-connect-btn'){ ev.preventDefault(); openPopup(); }
        if(t && t.id==='asyntai-reset'){ ev.preventDefault(); resetConnection(); }
        if(t && t.id==='asyntai-fallback-link'){ 
            // Re-generate state and update link when clicked
            currentState = generateState();
            updateFallbackLink();
            // Let the link work normally (target="_blank")
            // Also start polling for this state
            setTimeout(function(){ pollForConnection(currentState); }, 1000);
        }
    });
})();

