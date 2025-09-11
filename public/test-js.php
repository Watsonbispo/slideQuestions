<?php
echo "=== TESTE JAVASCRIPT RAILWAY ===<br><br>";

echo "1. Testando JavaScript básico:<br>";
echo "<script>console.log('JavaScript funcionando!');</script>";
echo "<div id='test-js'>Carregando...</div>";
echo "<script>document.getElementById('test-js').innerHTML = 'JavaScript OK!';</script>";

echo "<br><br>2. Testando fetch API:<br>";
echo "<script>
fetch('/q/1')
  .then(response => {
    console.log('Fetch funcionando:', response.status);
    document.getElementById('fetch-test').innerHTML = 'Fetch OK - Status: ' + response.status;
  })
  .catch(error => {
    console.error('Fetch error:', error);
    document.getElementById('fetch-test').innerHTML = 'Fetch ERROR: ' + error.message;
  });
</script>";
echo "<div id='fetch-test'>Testando fetch...</div>";

echo "<br><br>3. Testando event listeners:<br>";
echo "<button id='test-btn' onclick='testClick()'>Clique aqui</button>";
echo "<div id='click-test'>Nenhum clique</div>";
echo "<script>
function testClick() {
  document.getElementById('click-test').innerHTML = 'Clique funcionou!';
}
</script>";

echo "<br><br>4. Testando form submission:<br>";
echo "<form id='test-form' onsubmit='return testSubmit(event)'>";
echo "<input type='radio' name='test' value='1' id='test1'><label for='test1'>Opção 1</label><br>";
echo "<input type='radio' name='test' value='2' id='test2'><label for='test2'>Opção 2</label><br>";
echo "<button type='submit'>Testar Submit</button>";
echo "</form>";
echo "<div id='submit-test'>Nenhum submit</div>";
echo "<script>
function testSubmit(e) {
  e.preventDefault();
  document.getElementById('submit-test').innerHTML = 'Submit funcionou!';
  return false;
}
</script>";
?>
