<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cipher Lab</title>
  <link rel="stylesheet" href="index.css"/>
</head>
<body>
<div class="container">
  <div class="badge">// Encryption Toolkit</div>
  <h1>Cipher Lab</h1>
  <p class="subtitle">Classical cryptography — encrypt, decrypt, explore</p>

  <div class="cards">
    <a href="playfair.php" class="card card-1">
      <div class="card-icon">🔲</div>
      <div class="card-num">01 — Playfair</div>
      <div class="card-title">Playfair Cipher</div>
      <div class="card-desc">Digraph substitution using a 5×5 key matrix. Encrypts letter pairs instead of single letters.</div>
      <div class="card-arrow">Open →</div>
    </a>

    <a href="hill.php" class="card card-2">
      <div class="card-icon">🧮</div>
      <div class="card-num">02 — Hill</div>
      <div class="card-title">Hill Cipher</div>
      <div class="card-desc">Matrix-based polygraphic cipher. Uses linear algebra to transform blocks of letters.</div>
      <div class="card-arrow">Open →</div>
    </a>

    <a href="affine.php" class="card card-3">
      <div class="card-icon">✖️</div>
      <div class="card-num">03 — Affine</div>
      <div class="card-title">Affine Cipher</div>
      <div class="card-desc">Combines multiplication and addition using two keys A and B for monoalphabetic substitution.</div>
      <div class="card-arrow">Open →</div>
    </a>
  </div>

  <div class="footer">Cipher Lab — Classical Cryptography Tools</div>
</div>
</body>
</html>
