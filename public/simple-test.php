<!DOCTYPE html>
<html>
<head>
    <title>Teste Simples</title>
</head>
<body>
    <h1>Teste de Formulário Simples</h1>
    
    <form id="test-form" method="post" action="/q/1">
        <div>
            <input type="radio" name="answer" value="a" id="a">
            <label for="a">Opção A</label>
        </div>
        <div>
            <input type="radio" name="answer" value="b" id="b">
            <label for="b">Opção B</label>
        </div>
        <button type="submit" id="submit-btn" disabled>Responder</button>
    </form>
    
    <div id="status">Nenhuma opção selecionada</div>
    
    <script>
    console.log('Script carregado');
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado');
        
        const form = document.getElementById('test-form');
        const submitBtn = document.getElementById('submit-btn');
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        const status = document.getElementById('status');
        
        console.log('Form:', form);
        console.log('SubmitBtn:', submitBtn);
        console.log('RadioButtons:', radioButtons.length);
        
        // Clear any pre-selected radio buttons
        radioButtons.forEach(rb => rb.checked = false);
        submitBtn.disabled = true;

        // Enable/disable button based on selection
        radioButtons.forEach(rb => {
            rb.addEventListener('change', function() {
                console.log('Radio changed:', this.value);
                const anySelected = Array.from(radioButtons).some(rb => rb.checked);
                submitBtn.disabled = !anySelected;
                status.textContent = anySelected ? 'Opção selecionada!' : 'Nenhuma opção selecionada';
            });
        });
        
        console.log('Event listeners adicionados');
    });
    </script>
</body>
</html>
