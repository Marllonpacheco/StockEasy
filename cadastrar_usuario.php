<?php
// O cadastro com validação e password_hash fica centralizado em cadastro.php.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(405);
    exit('Use o formulário de cadastro.');
}

header('Location: cadastro.php');
exit;
