<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';

session_start();

use Services\{Locadora, Auth};
use Models\{Profissões, Personagens, Animais, Tematica};

// Verificar se está logado
if (!Auth::verificarLogin()) {
    header('Location: login.php');
    exit;
}

// Processar logout
if (isset($_GET['logout'])) {
    (new Auth())->logout();
    header('Location: login.php');
    exit;
}

// Instancia a Locadora
$locadora = new Locadora();
$mensagem = '';
$usuario = Auth::getUsuario();

// Processar requisições
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar permissões para ações administrativas
    if (isset($_POST['adicionar']) || isset($_POST['deletar']) || 
        isset($_POST['alugar']) || isset($_POST['devolver'])) {
        if (!Auth::isAdmin()) {
            $mensagem = "Você não tem permissão para realizar esta ação.";
            goto renderizar;
        }
    }

    if (isset($_POST['adicionar'])) {
        $nome = $_POST['nome'];
        $tamanho = $_POST['tamanho'];
        $tipo = $_POST['tipo'];

        switch ($tipo) {
            case 'Profissões':
                $fantasia = new Profissões($nome, $tamanho);
                break;
            case 'Animais':
                $fantasia = new Animais($nome, $tamanho);
                break;
            case 'Tematica':
                $fantasia = new Tematica($nome, $tamanho);
                break;
            default:
                $fantasia = new Personagens($nome, $tamanho);
                break;
        }
        $locadora->adicionarFantasia($fantasia);
        $mensagem = "Fantasia adicionada com sucesso!";
    } elseif (isset($_POST['alugar'])) {
        $dias = isset($_POST['dias']) ? (int)$_POST['dias'] : 1;
        $mensagem = $locadora->alugarFantasia($_POST['nome'], $dias);
    } elseif (isset($_POST['devolver'])) {
        $mensagem = $locadora->devolverFantasia($_POST['nome']);
    } elseif (isset($_POST['deletar'])) {
        $mensagem = $locadora->deletarFantasia($_POST['nome'], $_POST['tamanho']);
    } elseif (isset($_POST['calcular'])) {
        $dias = (int)$_POST['dias_calculo'];
        $tipo = $_POST['tipo_calculo'];
        $valor = $locadora->calcularPrevisaoAluguel($tipo, $dias);
        $mensagem = "Previsão de valor para {$dias} dias: R$ " . number_format($valor, 2, ',', '.');
    }
}

renderizar:
// Inclui o template da view
require_once __DIR__ . '/../views/template.php';
