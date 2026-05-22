<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Affine Cipher – CipherLab</title>
  <link rel="stylesheet" href="affine.css"/>
</head>
<body>
<div class="wrapper">

  <a href="index.php" class="back-link">◀ BACK TO CIPHER LAB</a>

  <div class="cipher-header">
    <div class="cipher-tag">// CIPHER 03</div>
    <div class="cipher-title">AFFINE CIPHER</div>
    <div class="cipher-subtitle">MULTIPLICATIVE + ADDITIVE SUBSTITUTION</div>
  </div>

  <div class="mode-toggle">
    <button class="mode-btn active" id="btn-encrypt" onclick="setMode('encrypt')">🔒 ENCRYPT</button>
    <button class="mode-btn" id="btn-decrypt" onclick="setMode('decrypt')">🔓 DECRYPT</button>
  </div>

  <div class="card">
    <div class="section-label">KEY CONFIGURATION</div>

    <div class="field-row field">
      <div>
        <label>KEY A — MULTIPLICATIVE</label>
        <input type="number" id="key-a" value="5" min="1" max="25" oninput="validateA()"/>
        <div class="hint" id="hint-a">// Must be coprime to 26</div>
        <div class="key-chips" id="a-chips"></div>
      </div>
      <div>
        <label>KEY B — SHIFT (0 – 25)</label>
        <input type="number" id="key-b" value="8" min="0" max="25"/>
        <div class="hint">// Any value from 0 to 25</div>
      </div>
    </div>

    <div class="field">
      <label id="input-label">PLAINTEXT</label>
      <textarea id="input-text" placeholder="Enter your message here..."></textarea>
    </div>

    <div class="btn-row">
      <button class="btn btn-primary" onclick="runCipher()">▶ RUN CIPHER</button>
      <button class="btn btn-ghost" onclick="clearAll()">CLEAR</button>
      <button class="btn btn-ghost" onclick="swapIO()">⇄ SWAP I/O</button>
    </div>
  </div>

  <div class="card">
    <div class="section-label">OUTPUT</div>
    <div class="output-box" id="output">
      <span class="output-empty">// result will appear here...</span>
      <button class="copy-btn" onclick="copyOutput()">COPY</button>
    </div>
  </div>

  <div class="info-box">
    <h3>HOW IT WORKS</h3>
    <p>
      The Affine cipher is a monoalphabetic substitution cipher using both multiplication and addition.
      Key A must be coprime to 26 so every letter maps to a unique ciphertext letter.
      Key B is a standard shift offset applied after multiplication.
    </p>
    <div class="formula">
      ENCRYPT: C = (A × P + B) mod 26<br>
      DECRYPT: P = A⁻¹ × (C − B) mod 26<br>
      <span style="color:var(--muted);font-size:0.68rem">P = plaintext position · A,B = keys · A⁻¹ = modular inverse of A mod 26</span>
    </div>
  </div>

</div>

<script>
const ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const VALID_A = [1,3,5,7,9,11,15,17,19,21,23,25];
let mode = 'encrypt';

const chipsEl = document.getElementById('a-chips');
VALID_A.forEach(v => {
  const chip = document.createElement('span');
  chip.className = 'key-chip' + (v === 5 ? ' active' : '');
  chip.textContent = v;
  chip.onclick = () => {
    document.getElementById('key-a').value = v;
    document.querySelectorAll('.key-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    validateA();
  };
  chipsEl.appendChild(chip);
});

function gcd(a, b) { return b === 0 ? a : gcd(b, a % b); }
function modInverse(a, m) {
  for (let x = 1; x < m; x++) if ((a * x) % m === 1) return x;
  return -1;
}

function setMode(m) {
  mode = m;
  document.getElementById('btn-encrypt').classList.toggle('active', m === 'encrypt');
  document.getElementById('btn-decrypt').classList.toggle('active', m === 'decrypt');
  document.getElementById('input-label').textContent = m === 'encrypt' ? 'PLAINTEXT' : 'CIPHERTEXT';
  document.getElementById('input-text').placeholder = m === 'encrypt' ? 'Enter your message here...' : 'Enter ciphertext to decrypt...';
}

function validateA() {
  const a = parseInt(document.getElementById('key-a').value);
  const hint = document.getElementById('hint-a');
  const isValid = VALID_A.includes(a);
  hint.textContent = isValid ? `// ✓ gcd(${a}, 26) = 1 — Valid key` : '// ✗ Must be coprime to 26: 1,3,5,7,9,11,15,17,19,21,23,25';
  hint.className = isValid ? 'hint ok' : 'hint error';
  document.querySelectorAll('.key-chip').forEach(c => {
    c.classList.toggle('active', parseInt(c.textContent) === a);
  });
}

function affineEncrypt(text, a, b) {
  return text.toUpperCase().split('').map(ch => {
    const idx = ALPHA.indexOf(ch);
    if (idx === -1) return ch;
    return ALPHA[(a * idx + b) % 26];
  }).join('');
}

function affineDecrypt(text, a, b) {
  const aInv = modInverse(a, 26);
  if (aInv === -1) return 'ERROR: NO MODULAR INVERSE FOR A';
  return text.toUpperCase().split('').map(ch => {
    const idx = ALPHA.indexOf(ch);
    if (idx === -1) return ch;
    return ALPHA[(aInv * (idx - b + 26)) % 26];
  }).join('');
}

function runCipher() {
  const text = document.getElementById('input-text').value.trim();
  const a = parseInt(document.getElementById('key-a').value);
  const b = parseInt(document.getElementById('key-b').value);
  if (!text) { setOutput('// NO INPUT PROVIDED', true); return; }
  if (!VALID_A.includes(a)) { setOutput('// INVALID KEY A — MUST BE COPRIME TO 26', true); return; }
  if (isNaN(b) || b < 0 || b > 25) { setOutput('// INVALID KEY B — MUST BE 0 TO 25', true); return; }
  const result = mode === 'encrypt' ? affineEncrypt(text, a, b) : affineDecrypt(text, a, b);
  setOutput(result, false);
}

function setOutput(val, empty) {
  const box = document.getElementById('output');
  box.innerHTML = (empty ? `<span class="output-empty">${val}</span>` : val) +
    `<button class="copy-btn" onclick="copyOutput()">COPY</button>`;
}

function clearAll() {
  document.getElementById('input-text').value = '';
  setOutput('// result will appear here...', true);
}

function swapIO() {
  const out = document.getElementById('output');
  const text = out.innerText.replace('COPY', '').trim();
  if (text && !text.startsWith('//')) {
    document.getElementById('input-text').value = text;
    setOutput('// result will appear here...', true);
  }
}

function copyOutput() {
  const out = document.getElementById('output');
  const text = out.innerText.replace('COPY', '').trim();
  if (text && !text.startsWith('//')) {
    navigator.clipboard.writeText(text);
    const btn = out.querySelector('.copy-btn');
    btn.textContent = 'COPIED!';
    setTimeout(() => btn.textContent = 'COPY', 1500);
  }
}

validateA();
</script>
</body>
</html>
