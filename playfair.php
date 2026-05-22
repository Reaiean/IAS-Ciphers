<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Playfair Cipher – CipherLab</title>
  <link rel="stylesheet" href="playfair.css"/>
</head>
<body>
<div class="wrapper">

  <a href="index.php" class="back-link">◀ BACK TO CIPHER LAB</a>

  <div class="cipher-header">
    <div class="cipher-tag">// CIPHER 01</div>
    <div class="cipher-title">PLAYFAIR CIPHER</div>
    <div class="cipher-subtitle">DIGRAPH SUBSTITUTION — 5×5 KEY MATRIX</div>
  </div>

  <div class="mode-toggle">
    <button class="mode-btn active" id="btn-encrypt" onclick="setMode('encrypt')">🔒 ENCRYPT</button>
    <button class="mode-btn" id="btn-decrypt" onclick="setMode('decrypt')">🔓 DECRYPT</button>
  </div>

  <div class="card">
    <div class="section-label">KEY CONFIGURATION</div>

    <div class="field">
      <label>KEYWORD</label>
      <input type="text" id="keyword" value="MONARCHY" placeholder="Enter keyword..." oninput="buildMatrix()"/>
      <div class="hint" id="hint-kw">// Letters only, duplicates removed, I/J share a cell</div>
    </div>

    <div class="field">
      <label>5×5 KEY MATRIX PREVIEW</label>
      <div class="pf-grid" id="pf-grid"></div>
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
      The Playfair cipher encrypts pairs of letters (digraphs) using a 5×5 matrix built from a keyword.
      I and J share one cell. The plaintext is split into pairs; repeated letters in a pair are separated by 'X'.
      Each pair is encrypted using three rules based on whether the letters share a row, column, or neither.
    </p>
    <div class="formula">
      SAME ROW → shift right (left to decrypt)<br>
      SAME COL → shift down (up to decrypt)<br>
      RECTANGLE → swap columns within the same rows<br>
      <span style="color:var(--muted);font-size:0.68rem">Padding: 'X' between doubles · 'Z' at end if odd length</span>
    </div>
  </div>

</div>

<script>
const ALPHA25 = 'ABCDEFGHIKLMNOPQRSTUVWXYZ'; // no J
let matrix = [];
let mode = 'encrypt';

function buildMatrix() {
  const kw = (document.getElementById('keyword').value.toUpperCase() + ALPHA25)
    .replace(/J/g, 'I')
    .replace(/[^A-Z]/g, '');
  const seen = new Set();
  const letters = [];
  for (const ch of kw) {
    if (!seen.has(ch)) { seen.add(ch); letters.push(ch); }
    if (letters.length === 25) break;
  }
  matrix = [];
  for (let r = 0; r < 5; r++) matrix.push(letters.slice(r * 5, r * 5 + 5));

  const grid = document.getElementById('pf-grid');
  grid.innerHTML = '';
  const keyword = document.getElementById('keyword').value.toUpperCase().replace(/J/g, 'I').replace(/[^A-Z]/g, '');
  const kwSet = new Set(keyword);
  letters.forEach(ch => {
    const cell = document.createElement('div');
    cell.className = 'pf-cell' + (kwSet.has(ch) ? ' active-key' : '');
    cell.textContent = ch;
    grid.appendChild(cell);
  });
}

function pos(ch) {
  ch = ch === 'J' ? 'I' : ch;
  for (let r = 0; r < 5; r++)
    for (let c = 0; c < 5; c++)
      if (matrix[r][c] === ch) return [r, c];
  return [-1, -1];
}

function prepareText(text) {
  text = text.toUpperCase().replace(/J/g, 'I').replace(/[^A-Z]/g, '');
  let result = '';
  let i = 0;
  while (i < text.length) {
    const a = text[i];
    const b = text[i + 1] || 'Z';
    if (a === b) { result += a + 'X'; i++; }
    else { result += a + b; i += 2; }
  }
  if (result.length % 2 !== 0) result += 'Z';
  return result;
}

function processDigraph(a, b, enc) {
  const [ra, ca] = pos(a);
  const [rb, cb] = pos(b);
  const d = enc ? 1 : -1;
  if (ra === rb) return matrix[ra][(ca + d + 5) % 5] + matrix[rb][(cb + d + 5) % 5];
  if (ca === cb) return matrix[(ra + d + 5) % 5][ca] + matrix[(rb + d + 5) % 5][cb];
  return matrix[ra][cb] + matrix[rb][ca];
}

function setMode(m) {
  mode = m;
  document.getElementById('btn-encrypt').classList.toggle('active', m === 'encrypt');
  document.getElementById('btn-decrypt').classList.toggle('active', m === 'decrypt');
  document.getElementById('input-label').textContent = m === 'encrypt' ? 'PLAINTEXT' : 'CIPHERTEXT';
  document.getElementById('input-text').placeholder = m === 'encrypt' ? 'Enter your message here...' : 'Enter ciphertext to decrypt...';
}

function runCipher() {
  const raw = document.getElementById('input-text').value.trim();
  if (!raw) { setOutput('// NO INPUT PROVIDED', true); return; }
  const text = mode === 'encrypt' ? prepareText(raw) : raw.toUpperCase().replace(/[^A-Z]/g, '');
  let result = '';
  for (let i = 0; i < text.length; i += 2)
    result += processDigraph(text[i], text[i + 1] || 'Z', mode === 'encrypt') + ' ';
  setOutput(result.trim(), false);
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

buildMatrix();
</script>
</body>
</html>
