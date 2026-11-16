(function () {
	'use strict';

	var API = window.VRAPI;
	var VERIFY_URL = (window.VR_CONFIG && window.VR_CONFIG.verifyUrl) || 'verify.html';

	var els = {
		name: document.getElementById('name'),
		min: document.getElementById('minTxt'),
		max: document.getElementById('maxTxt'),
		generate: document.getElementById('generateBtn'),
		save: document.getElementById('saveBtn'),
		info: document.getElementById('info'),
		blue: document.getElementById('blue'),
		bottom: document.getElementById('bottom'),
		drawList: document.getElementById('drawList'),
		codeWrapper: document.getElementById('codeWrapper'),
		code: document.getElementById('code')
	};

	var session = { draws: [], code: null, counter: 0 };

	function markField(field) { field.style.borderColor = 'red'; }
	function unmarkField() { this.style.borderColor = ''; }
	els.min.addEventListener('click', unmarkField);
	els.max.addEventListener('click', unmarkField);

	function validation() {
		var min = parseInt(els.min.value, 10);
		var max = parseInt(els.max.value, 10);
		if (isNaN(min)) { markField(els.min); els.info.textContent = 'Min value is required.'; return null; }
		if (isNaN(max)) { markField(els.max); els.info.textContent = 'Max value is required.'; return null; }
		if (min >= max) {
			markField(els.min); markField(els.max);
			els.info.textContent = 'Min value must be lower than Max value.';
			return null;
		}
		return { name: els.name.value.trim(), min: min, max: max };
	}

	function resetSession() {
		session.draws = [];
		session.code = null;
		session.counter = 0;
		els.drawList.innerHTML = '';
		els.codeWrapper.classList.add('hidden');
		els.code.textContent = '';
		els.code.removeAttribute('href');
		els.bottom.classList.add('resultHidden');
		els.generate.textContent = 'Generate';
		setSaveMode('save');
	}

	function setSaveMode(mode) {
		if (mode === 'copy') {
			els.save.textContent = 'Copy Code';
			els.info.textContent = 'Verification code generated. Press COPY CODE to copy it, then verify it at /verify.';
		} else {
			els.save.textContent = 'Save';
			els.info.textContent = 'Press SAVE when completed to generate verification code';
		}
	}

	function ribbon() {
		els.blue.style.transition = 'width 0.4s ease';
		els.blue.style.width = '100%';
		setTimeout(function () {
			els.blue.style.transition = 'none';
			els.blue.style.width = '0';
		}, 450);
	}

	function renderDraws() {
		els.drawList.innerHTML = '';
		session.draws.forEach(function (d) {
			var row = document.createElement('div');
			row.className = 'drawRow';

			var num = document.createElement('div');
			num.className = 'drawNum';
			num.textContent = d.result;

			var meta = document.createElement('div');
			meta.className = 'drawMeta';
			if (d.name) {
				var nameLine = document.createElement('div');
				nameLine.textContent = d.name;
				meta.appendChild(nameLine);
			}
			var rangeLine = document.createElement('div');
			rangeLine.textContent = 'Min ' + d.min + ' \u2013 Max ' + d.max;
			meta.appendChild(rangeLine);
			var timeLine = document.createElement('div');
			timeLine.textContent = d.time + ' UTC';
			meta.appendChild(timeLine);

			row.appendChild(num);
			row.appendChild(meta);
			els.drawList.appendChild(row);
		});
	}

	function copyText(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text).then(function () { return true; }).catch(function () { return legacyCopy(text); });
		}
		return Promise.resolve(legacyCopy(text));
	}

	function legacyCopy(text) {
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.style.position = 'fixed';
		ta.style.opacity = '0';
		document.body.appendChild(ta);
		ta.focus();
		ta.select();
		var ok = false;
		try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
		document.body.removeChild(ta);
		return ok;
	}

	els.generate.addEventListener('click', function () {
		var input = validation();
		if (!input) return;
		if (session.code) resetSession(); // already saved, start over

		session.counter++;
		els.generate.textContent = 'Generate (' + session.counter + ')';
		ribbon();

		els.info.textContent = 'Drawing number\u2026';
		API.draw(input).then(function (res) {
			if (!res.ok) {
				els.info.textContent = res.error || 'Could not generate a number. Please try again.';
				return;
			}
			session.draws.push({ name: input.name, min: input.min, max: input.max, result: res.result, time: res.time });
			renderDraws();
			els.bottom.classList.remove('resultHidden');
			els.save.classList.remove('hiddenBtn');
			setSaveMode('save');
			els.info.textContent = session.draws.length + ' number' + (session.draws.length > 1 ? 's' : '') +
				' drawn. Generate more, or press SAVE to get your verification code.';
		});
	});

	els.save.addEventListener('click', function () {
		if (session.code) { copyCode(); return; }
		if (session.draws.length === 0) return;

		els.info.textContent = 'Generating verification code\u2026';
		API.save(session.draws).then(function (res) {
			if (!res.ok) {
				els.info.textContent = res.error || 'Could not save. Please try again.';
				return;
			}
			session.code = res.code;
			els.code.textContent = res.code;
			els.code.href = VERIFY_URL + '?code=' + encodeURIComponent(res.code);
			els.codeWrapper.classList.remove('hidden');
			setSaveMode('copy');
		});
	});

	function copyCode() {
		copyText(session.code).then(function (ok) {
			els.info.textContent = ok
				? 'Verification code copied to clipboard: ' + session.code
				: 'Copy your verification code: ' + session.code;
		});
	}
})();
