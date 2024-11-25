 function loadScript(src, done) {
    let js = document.createElement('script');
    js.src = src;
    js.onload = function() {
        done();
    };
    js.onerror = function() {
        done(new Error('Failed to load script ' + src));
    };
    document.head.appendChild(js);
}
let loaded = false;

        window.addEventListener("loaded",()=> {
            window.clearInterval(window.i1);
            if (!loaded && window.md !== 1) {
                setTimeout(() => {
                    loadScript('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', () => {
                        loaded = true;
                        console.log("finished");
                    });
                }, 10);
            }

        });


