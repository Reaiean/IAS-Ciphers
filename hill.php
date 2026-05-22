<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hill Cipher – CipherLab</title>
  <link rel="stylesheet" href="hill.css"/>
  <style>
    .size-toggle { display:flex; gap:6px; margin-bottom:1rem; }
    .size-btn {
      font-family: var(--mono);
      font-size: 0.72rem;
      font-weight: 700;
      padding: 0.35rem 1rem;
      border-radius: 6px;
      border: 1px solid var(--border);
      background: var(--bg);
      color: var(--muted);
      cursor: pointer;
      transition: all 0.15s;
    }
    .size-btn.active { background: var(--accent); color: #000; border-color: var(--accent); }
  </style>
</head>
<body>
<div class="wrapper">

  <a href="index.php" class="back-link">◀ BACK TO CIPHER LAB</a>

  <div class="cipher-header">
    <div class="cipher-tag">// CIPHER 02</div>
    <div class="cipher-title">HILL CIPHER</div>
    <div class="cipher-subtitle">MATRIX-BASED POLYGRAPHIC ENCRYPTION</div>
  </div>

  <div class="mode-toggle">
    <button class="mode-btn active" id="btn-encrypt" onclick="setMode('encrypt')">🔒 ENCRYPT</button>
    <button class="mode-btn" id="btn-decrypt" onclick="setMode('decrypt')">🔓 DECRYPT</button>
  </div>

  <div class="card">
    <div class="section-label">KEY MATRIX</div>

    <div class="field">
      <label>MATRIX SIZE</label>
      <div class="size-toggle">
        <button class="size-btn active" id="sz2" onclick="setSize(2)">2 × 2</button>
        <button class="size-btn" id="sz3" onclick="setSize(3)">3 × 3</button>
      </div>
    </div>

    <div class="field">
      <label>KEY VALUES (mod 26)</label>
      <div class="matrix-grid" id="matrix-grid"></div>
      <div class="det-hint" id="det-hint"></div>
      <div class="hint" style="margin-top:0.3rem">// det(K) must be coprime to 26 for the matrix to be invertible</div>
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
      The Hill cipher uses an n×n key matrix to encrypt blocks of n letters at a time.
      Each letter is converted to a number (A=0…Z=25), multiplied by the key matrix mod 26,
      and converted back to letters. Decryption uses the modular inverse of the key matrix.
    </p>
    <div class="formula">
      ENCRYPT: C = K × P (mod 26)<br>
      DECRYPT: P = K⁻¹ × C (mod 26)<br>
      <span style="color:var(--muted);font-size:0.68rem">K = key matrix · P/C = column vector of letter numbers · K⁻¹ = modular inverse matrix</span>
    </div>
  </div>

</div>

<script>
const ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
let mode = 'encrypt';
let matSize = 2;
let cells = [];

function setSize(n) {
  matSize = n;
  document.getElementById('sz2').classList.toggle('active', n === 2);
  document.getElementById('sz3').classList.toggle('active', n === 3);
  buildMatrixUI();
}

function buildMatrixUI() {
  const grid = document.getElementById('matrix-grid');
  grid.innerHTML = '';
  cells = [];
  const defaults2 = [3,3,2,5];
  const defaults3 = [6,24,1,13,16,10,20,17,15];
  const defs = matSize === 2 ? defaults2 : defaults3;
  const group = document.createElement('div');
  group.className = 'matrix-group';
  for (let r = 0; r < matSize; r++) {
    const row = document.createElement('div');
    row.className = 'matrix-row';
    for (let c = 0; c < matSize; c++) {
      const inp = document.createElement('input');
      inp.type = 'number';
      inp.className = 'matrix-cell';
      inp.min = 0; inp.max = 25;
      inp.value = defs[r * matSize + c];
      inp.oninput = validateMatrix;
      cells.push(inp);
      row.appendChild(inp);
    }
    group.appendChild(row);
  }
  const lbl = document.createElement('div');
  lbl.className = 'matrix-label';
  lbl.textContent = `KEY MATRIX K (${matSize}×${matSize})`;
  group.appendChild(lbl);
  grid.appendChild(group);
  validateMatrix();
}

function getMatrix() {
  const k = [];
  cells.forEach(c => k.push(((parseInt(c.value) || 0) % 26 + 26) % 26));
  return k;
}

function gcd(a, b) { return b === 0 ? a : gcd(b, a % b); }
function modInv(a, m) {
  a = ((a % m) + m) % m;
  for (let x = 1; x < m; x++) if ((a * x) % m === 1) return x;
  return -1;
}

function det2(m) { return m[0]*m[3] - m[1]*m[2]; }
function det3(m) {
  return m[0]*(m[4]*m[8]-m[5]*m[7]) - m[1]*(m[3]*m[8]-m[5]*m[6]) + m[2]*(m[3]*m[7]-m[4]*m[6]);
}
function getDet(k) { return matSize === 2 ? det2(k) : det3(k); }

function invertMatrix(k) {
  if (matSize === 2) {
    const det = ((det2(k) % 26) + 26) % 26;
    const detInv = modInv(det, 26);
    if (detInv === -1) return null;
    return [
      (detInv * k[3] % 26 + 26) % 26,
      (detInv * (-k[1] % 26 + 26) % 26),
      (detInv * (-k[2] % 26 + 26) % 26),
      (detInv * k[0] % 26 + 26) % 26
    ];
  } else {
    const det = ((det3(k) % 26) + 26) % 26;
    const detInv = modInv(det, 26);
    if (detInv === -1) return null;
    // cofactor matrix
    const cof = [
      k[4]*k[8]-k[5]*k[7], -(k[3]*k[8]-k[5]*k[6]), k[3]*k[7]-k[4]*k[6],
      -(k[1]*k[8]-k[2]*k[7]), k[0]*k[8]-k[2]*k[6], -(k[0]*k[7]-k[1]*k[6]),
      k[1]*k[5]-k[2]*k[4], -(k[0]*k[5]-k[2]*k[3]), k[0]*k[4]-k[1]*k[3]
    ];
    // transpose (adjugate)
    const adj = [cof[0],cof[3],cof[6], cof[1],cof[4],cof[7], cof[2],cof[5],cof[8]];
    return adj.map(v => ((detInv * ((v % 26 + 26) % 26)) % 26 + 26) % 26);
  }
}

function matMulVec(k, vec) {
  const n = matSize;
  const res = [];
  for (let r = 0; r < n; r++) {
    let s = 0;
    for (let c = 0; c < n; c++) s += k[r * n + c] * vec[c];
    res.push(((s % 26) + 26) % 26);
  }
  return res;
}

function validateMatrix() {
  const k = getMatrix();
  const det = getDet(k);
  const detMod = ((det % 26) + 26) % 26;
  const g = gcd(detMod, 26);
  const hint = document.getElementById('det-hint');
  if (g === 1) {
    hint.textContent = `✓ det(K) = ${detMod} mod 26 — invertible (gcd=${g})`;
    hint.style.color = '#4ade80';
  } else {
    hint.textContent = `✗ det(K) = ${detMod} mod 26 — NOT invertible (gcd=${g}, must be 1)`;
    hint.style.color = '#f87171';
  }
}

function hillEncrypt(text, k) {
  text = text.toUpperCase().replace(/[^A-Z]/g, '');
  while (text.length % matSize !== 0) text += 'X';
  let result = '';
  for (let i = 0; i < text.length; i += matSize) {
    const vec = Array.from({length: matSize}, (_, j) => ALPHA.indexOf(text[i + j]));
    matMulVec(k, vec).forEach(n => result += ALPHA[n]);
  }
  return result;
}

function hillDecrypt(text, k) {
  const kInv = invertMatrix(k);
  if (!kInv) return 'ERROR: KEY MATRIX NOT INVERTIBLE MOD 26';
  text = text.toUpperCase().replace(/[^A-Z]/g, '');
  while (text.length % matSize !== 0) text += 'X';
  let result = '';
  for (let i = 0; i < text.length; i += matSize) {
    const vec = Array.from({length: matSize}, (_, j) => ALPHA.indexOf(text[i + j]));
    matMulVec(kInv, vec).forEach(n => result += ALPHA[n]);
  }
  return result;
}

function setMode(m) {
  mode = m;
  document.getElementById('btn-encrypt').classList.toggle('active', m === 'encrypt');
  document.getElementById('btn-decrypt').classList.toggle('active', m === 'decrypt');
  document.getElementById('input-label').textContent = m === 'encrypt' ? 'PLAINTEXT' : 'CIPHERTEXT';
  document.getElementById('input-text').placeholder = m === 'encrypt' ? 'Enter your message here...' : 'Enter ciphertext to decrypt...';
}

function runCipher() {
  const text = document.getElementById('input-text').value.trim();
  const k = getMatrix();
  if (!text) { setOutput('// NO INPUT PROVIDED', true); return; }
  const det = ((getDet(k) % 26) + 26) % 26;
  if (gcd(det, 26) !== 1) { setOutput('// KEY MATRIX NOT INVERTIBLE — adjust values', true); return; }
  const result = mode === 'encrypt' ? hillEncrypt(text, k) : hillDecrypt(text, k);
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

buildMatrixUI();
</script>
</body>
</html>
