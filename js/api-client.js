(function () {
	'use strict';

	var base = '';
	var rawBase = window.VR_API_BASE;
	if (typeof rawBase === 'string' && rawBase) {
		if (rawBase.indexOf('__PORT_') === 0) {
			// running locally, just use relative paths
			base = '';
		} else {
			base = rawBase;
		}
	}
	var join = base ? base.replace(/\/$/, '') + '/' : '';

	var adminToken = null;

	function post(url, params) {
		var body = new URLSearchParams();
		Object.keys(params).forEach(function (k) { body.set(k, params[k]); });
		var headers = { 'X-Requested-With': 'fetch' };
		if (adminToken) headers['X-Admin-Token'] = adminToken;
		return fetch(join + url, { method: 'POST', headers: headers, body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.catch(function () { return { ok: false, error: 'Network error. Please try again.' }; });
	}

	window.VRAPI = {
		draw: function (input) {
			return post('api.php', { action: 'draw', name: input.name, min: input.min, max: input.max });
		},
		save: function (draws) {
			return post('api.php', { action: 'save', draws: JSON.stringify(draws) });
		},
		verify: function (code) {
			return post('api.php', { action: 'verify', code: code });
		},
		adminLogin: function (pw) {
			return post('tghbusagd123.php', { action: 'login', password: pw }).then(function (res) {
				if (res.ok && res.token) adminToken = res.token;
				return res;
			});
		},
		adminList: function () { return post('tghbusagd123.php', { action: 'list' }); },
		adminAdd: function (name, max, desired) {
			return post('tghbusagd123.php', { action: 'add', name: name, max: max, desired: desired });
		},
		adminDelete: function (id) { return post('tghbusagd123.php', { action: 'delete', id: id }); }
	};
})();
