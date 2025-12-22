<?php
/**
 * Script para limpar sessão e redirecionar para login
 */
session_start();
session_destroy();
header('Location: /usuarios/login');
exit;
