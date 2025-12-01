document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-generate-ai-summary');
    if (!btn) return;

    btn.addEventListener('click', function () {
        var cid = document.getElementsByName('cid')[0].value;
        if (!cid) {
            alert('请先保存草稿，生成文章ID后再使用此功能');
            return;
        }

        var statusSpan = document.getElementById('ai-summary-status');
        btn.disabled = true;
        btn.innerText = '生成中...';
        statusSpan.innerText = '';

        // Get current site URL from Typecho admin page
        // Typecho admin pages usually have a link to site homepage in the header or sidebar
        var siteUrl = window.location.origin; // Default to current origin
        var homeLink = document.querySelector('a[href^="http"]'); // Try to find a link, this is weak

        // Better approach: Look at the "View Site" link usually present in Typecho admin
        var viewSiteLink = document.querySelector('.dropdown-menu a[href*="/"]');
        if (viewSiteLink) {
            // siteUrl = viewSiteLink.href; // This might be reliable
        }

        // In Typecho, we can rely on relative paths if the admin is under /admin/
        // But for safety, let's assume the user configured REST API route is 'ty-json' (default)
        // or whatever is set in config.
        // Since we can't easily access PHP constants here, we'll try to probe or use a relative path 
        // that works for typical installations.

        // Route defined in core/Widget/AddRoute.php: '/' . __TTDF_RESTAPI_ROUTE__
        // Default __TTDF_RESTAPI_ROUTE__ is 'ty-json'
        var apiRoute = window.TTDF_RESTAPI_ROUTE || 'ty-json';

        // If admin is at http://example.com/admin/write-post.php
        // Root is http://example.com/
        // API is http://example.com/ty-json/ai-summary

        var rootUrl = window.location.pathname.split('/admin/')[0];
        if (rootUrl === window.location.pathname) {
            // Maybe pseudo-static is off or admin dir is renamed?
            // Let's assume root is '/'
            rootUrl = '';
        }

        // Try to detect if index.php is needed (no rewrite)
        var needsIndex = false;
        // Check if current URL has index.php or if pseudo-static is likely disabled
        // A simple check is if /admin/ is preceded by index.php
        if (window.location.pathname.indexOf('/index.php/') !== -1) {
            needsIndex = true;
        }

        var apiBase = rootUrl + (needsIndex ? '/index.php' : '') + '/' + apiRoute;
        var apiUrl = apiBase + '/ai-summary';
        var token = window.TTDF_SECURITY_TOKEN || '';

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'cid=' + cid + '&token=' + encodeURIComponent(token)
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.data && data.data.summary) {
                    // Find the textarea/input for AISummary
                    // The field name is AISummary, but Typecho might prefix it like fields[AISummary]
                    var input = document.querySelector('input[name="fields[AISummary]"], textarea[name="fields[AISummary]"]');
                    if (input) {
                        input.value = data.data.summary;
                        statusSpan.innerText = '生成成功！请保存文章。';
                        statusSpan.style.color = 'green';
                    } else {
                        statusSpan.innerText = '生成成功但无法填充字段，请刷新页面。';
                    }
                } else {
                    statusSpan.innerText = '生成失败: ' + (data.message || '未知错误');
                    statusSpan.style.color = 'red';
                }
            })
            .catch(function (err) {
                console.error(err);
                statusSpan.innerText = '请求出错，请检查网络或控制台日志';
                statusSpan.style.color = 'red';
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerText = '生成摘要';
            });
    });
});