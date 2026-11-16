(function () {
	'use strict';

	var API = window.VRAPI;

	var els = {
		input: document.getElementById('codeInput'),
		btn: document.getElementById('btn'),
		statusLine: document.getElementById('statusLine'),
		displayCode: document.getElementById('displayCode'),
		badge: document.getElementById('badge'),
		info: document.getElementById('verifyInfo'),
		tableWrap: document.getElementById('tableWrap')
	};

	function esc(s) {
		return String(s === null || s === undefined ? '' : s);
	}

	function doVerify() {
		var code = els.input.value.trim();
		if (!code) return;

		els.statusLine.classList.add('hidden');
		els.info.classList.remove('hidden');
		els.info.textContent = 'Checking code\u2026';
		els.tableWrap.classList.add('hidden');

		API.verify(code).then(function (res) {
			if (!res.ok) {
				els.info.textContent = res.error || 'Something went wrong. Please try again.';
				return;
			}
			if (!res.found) {
				els.statusLine.classList.add('hidden');
				els.tableWrap.classList.add('hidden');
				els.info.textContent = 'No drawing was found for code "' + code + '". Check the code and try again.';
				return;
			}
			els.info.classList.add('hidden');
			els.displayCode.textContent = res.draws[0].code;
			els.badge.textContent = 'VERIFIED';
			els.badge.className = 'verified';
			els.statusLine.classList.remove('hidden');

			var wrap = document.createElement('div');
			var head = document.createElement('div');
			head.className = 'trow head';
			['#', 'Name', 'Min', 'Max', 'Date', 'Time (UTC)', 'Result'].forEach(function (t) {
				var c = document.createElement('div');
				c.textContent = t;
				head.appendChild(c);
			});
			wrap.appendChild(head);

			res.draws.forEach(function (d, i) {
				var parts = esc(d.time).split(' ');
				var row = document.createElement('div');
				row.className = 'trow';
				[
					{ label: '#', value: i + 1, cls: 'num' },
					{ label: 'Name', value: d.name || '\u2014' },
					{ label: 'Min', value: d.min },
					{ label: 'Max', value: d.max },
					{ label: 'Date', value: parts[0] },
					{ label: 'Time', value: parts[1] + ' UTC' },
					{ label: 'Result', value: d.result, cls: 'res' }
				].forEach(function (col) {
					var c = document.createElement('div');
					c.textContent = col.value;
					c.setAttribute('data-label', col.label);
					if (col.cls) c.className = col.cls;
					row.appendChild(c);
				});
				wrap.appendChild(row);
			});

			els.tableWrap.innerHTML = '';
			els.tableWrap.appendChild(wrap);
			els.tableWrap.classList.remove('hidden');
		});
	}

	els.btn.addEventListener('click', doVerify);
	els.input.addEventListener('keydown', function (e) {
		if (e.key === 'Enter') doVerify();
	});

	// /verify?code=... deep links
	var m = location.search.match(/[?&]code=([^&]+)/);
	if (m) {
		els.input.value = decodeURIComponent(m[1]);
		doVerify();
	}
})();
