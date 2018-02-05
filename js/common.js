$(function() {

    function codeTogglerPre(elem) {
        return $(elem).parent().next("pre");
    }
    
    $(".toggle-code").each(function() {
        codeTogglerPre(this).hide(0);
        $(this).click(function(evt) {codeTogglerPre(evt.target).toggle(1000);});
    });
    
    function tocElement(toc, idx, e) {
        var prefix = '';
        for (var i = e.tagName.substr(1) - 1; i > 0; i--) {
            prefix += '&nbsp;&nbsp;&nbsp;';
        }
        var ee = $(e);
        var name = 'toc-tgt-' + idx;
        ee.attr('id', name);
        prefix += '- <a href="' + location.href.replace(/\#.*/, '') + '#';
        toc.append(prefix + name + '">'+ ee.text() + '<a/><br/>');
    }
    
    function tableOfContent() {
        var parts = $('h2,h3');
        var title = $('h1:first');
        if (parts.size() < 1 || title.size() < 1 || title.attr('data-toc') == 'off') {
            return;
        }
        var toc = $('<div id="toc"><strong>Table of Contents</strong><br/></div>')
            .insertAfter('h1:first');
        parts.each(function(i, e) { tocElement(toc, i, e) });
    }
    
    tableOfContent();
});

