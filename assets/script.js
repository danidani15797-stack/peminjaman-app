document.addEventListener('DOMContentLoaded', function () {
    // Mobile sidebar toggle
    var menuBtn = document.querySelector('.menu-btn');
    var sidebar = document.querySelector('.sidebar');
    var scrim = document.querySelector('.scrim');
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (scrim) scrim.classList.toggle('show');
        });
    }
    if (scrim) {
        scrim.addEventListener('click', function () {
            sidebar.classList.remove('open');
            scrim.classList.remove('show');
        });
    }

    // Live clock
    var clock = document.querySelector('.clock');
    if (clock) {
        var render = function () {
            var d = new Date();
            var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][d.getDay()];
            var pad = function (n) { return n < 10 ? '0' + n : n; };
            clock.textContent = hari + ', ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        };
        render();
        setInterval(render, 1000);
    }

    // Count-up animation for dashboard stat tiles
    document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var duration = 900;
        var start = null;
        function step(ts) {
            if (!start) start = ts;
            var progress = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    });

    // Confirm before any destructive action
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('submit', function (e) {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // Live client-side filter for simple search boxes
    document.querySelectorAll('[data-table-search]').forEach(function (input) {
        var tableSel = input.getAttribute('data-table-search');
        var table = document.querySelector(tableSel);
        if (!table) return;
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            table.querySelectorAll('tbody tr').forEach(function (tr) {
                var text = tr.textContent.toLowerCase();
                tr.style.display = text.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    });
});
