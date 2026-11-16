(function () {
	'use strict';

	var API = window.VRAPI;

	var els = {
		gate: document.getElementById('gate'),
		gateForm: document.getElementById('gateForm'),
		pw: document.getElementById('pw'),
		gateMsg: document.getElementById('gateMsg'),
		panel: document.getElementById('panel'),
		addForm: document.getElementById('addForm'),
		aName: document.getElementById('aName'),
		aMax: document.getElementById('aMax'),
		aDesired: document.getElementById('aDesired'),
		addMsg: document.getElementById('addMsg'),
		entryBody: document.getElementById('entryBody'),
		emptyMsg: document.getElementById('emptyMsg')
	};

	function msg(el, text, cls) {
		el.textContent = text;
		el.className = cls || '';
	}

	function loadEntries() {
		API.adminList().then(function (res) {
			if (!res.ok) { showGate(); return; }
			showPanel();
			els.entryBody.innerHTML = '';
			if (!res.entries.length) {
				els.emptyMsg.classList.remove('hidden');
				return;
			}
			els.emptyMsg.classList.add('hidden');
			res.entries.forEach(function (e) {
				var tr = document.createElement('tr');

				[['name', e.name], ['max', e.max], ['desired', e.desired], ['created', e.created + ' UTC']].forEach(function (pair) {
					var td = document.createElement('td');
					td.textContent = pair[1];
					tr.appendChild(td);
				});

				var tdAction = document.createElement('td');
				tdAction.className = 'actions';
				var del = document.createElement('button');
				del.className = 'del';
				del.textContent = 'Delete';
				var armed = false;
				var disarmTimer = null;
				del.addEventListener('click', function () {
					if (!armed) {
						// second click confirms, so no accidental deletes
						armed = true;
						del.textContent = 'Confirm?';
						del.classList.add('armed');
						disarmTimer = setTimeout(function () {
							armed = false;
							del.textContent = 'Delete';
							del.classList.remove('armed');
						}, 3000);
						return;
					}
					clearTimeout(disarmTimer);
					API.adminDelete(e.id).then(function (r) {
						if (r.ok) loadEntries();
						else msg(els.addMsg, r.error || 'Delete failed.', 'err');
					});
				});
				tdAction.appendChild(del);
				tr.appendChild(tdAction);

				els.entryBody.appendChild(tr);
			});
		});
	}

	function showGate() { els.gate.classList.remove('hidden'); els.panel.classList.add('hidden'); }
	function showPanel() { els.gate.classList.add('hidden'); els.panel.classList.remove('hidden'); }

	els.gateForm.addEventListener('submit', function () {
		msg(els.gateMsg, 'Checking\u2026');
		API.adminLogin(els.pw.value).then(function (res) {
			if (res.ok) { els.pw.value = ''; loadEntries(); }
			else msg(els.gateMsg, res.error || 'Incorrect password.', 'err');
		});
	});

	els.addForm.addEventListener('submit', function () {
		var name = els.aName.value.trim();
		var max = parseInt(els.aMax.value, 10);
		var desired = parseInt(els.aDesired.value, 10);
		if (!name || isNaN(max) || isNaN(desired)) {
			msg(els.addMsg, 'All three fields are required.', 'err');
			return;
		}
		API.adminAdd(name, max, desired).then(function (res) {
			if (res.ok) {
				msg(els.addMsg, 'Added. Drawings with Name "' + name + '" and Max ' + max + ' will now return ' + desired + '.', 'ok');
				els.aName.value = ''; els.aMax.value = ''; els.aDesired.value = '';
				loadEntries();
			} else {
				msg(els.addMsg, res.error || 'Could not add entry.', 'err');
			}
		});
	});

	loadEntries();
})();
