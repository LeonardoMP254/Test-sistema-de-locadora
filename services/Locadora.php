<?php
namespace Services;
use Models\{ Veiculo, Profissões, Personagens, Animais, Tematica};

/**
 * Classe responsável por gerenciar as operações da locadora de fantasias
 */
class Locadora {
    private array $fantasias = [];

    public function __construct() {
        $this->carregarFantasias();
    }

    /**
     * Carrega as fantasias do arquivo JSON
     */
    private function carregarFantasias(): void {
        if (file_exists(ARQUIVO_JSON)) {
            $dados = json_decode(file_get_contents(ARQUIVO_JSON), true);
            foreach ($dados as $dado) {
                switch ($dado['tipo']) {
                    case 'Profissões':
                        $fantasia = new Profissões($dado['nome'], $dado['tamanho']);
                        break;
                    case 'Animais':
                        $fantasia = new Animais($dado['nome'], $dado['tamanho']);
                        break;
                    case 'Tematica':
                        $fantasia = new Tematica($dado['nome'], $dado['tamanho']);
                        break;
                    default:
                        $fantasia = new Personagens($dado['nome'], $dado['tamanho']);
                        break;
                }
                $fantasia->setDisponivel($dado['status']);
                $this->fantasias[] = $fantasia;
            }
        }
    }

    /**
     * Salva as fantasias no arquivo JSON
     */
    private function salvarFantasias(): void {
        $dados = [];
        foreach ($this->fantasias as $fantasia) {
            $dados[] = [
                'tipo' => $fantasia->getTipo(),
                'nome' => $fantasia->getNome(),
                'tamanho' => $fantasia->getTamanho(),
                'status' => $fantasia->isDisponivel()
            ];
        }
        
        // Cria o diretório se não existir
        $dir = dirname(ARQUIVO_JSON);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents(ARQUIVO_JSON, json_encode($dados, JSON_PRETTY_PRINT));
    }

    /**
     * Adiciona uma nova fantasia à locadora
     */
    public function adicionarFantasia(Veiculo $fantasia): void {
        $this->fantasias[] = $fantasia;
        $this->salvarFantasias();
    }

    /**
     * Remove uma fantasia da locadora
     */
    public function deletarFantasia(string $nome, string $tamanho): string {
        foreach ($this->fantasias as $key => $fantasia) {
            if ($fantasia->getNome() === $nome && $fantasia->getTamanho() === $tamanho) {
                unset($this->fantasias[$key]);
                $this->fantasias = array_values($this->fantasias);
                $this->salvarFantasias();
                return "Fantasia '{$nome}' removida com sucesso!";
            }
        }
        return "Fantasia não encontrada.";
    }

    /**
     * Aluga uma fantasia por um número específico de dias
     */
    public function alugarFantasia(string $nome, int $dias = 1): string {
        foreach ($this->fantasias as $fantasia) {
            if ($fantasia->getNome() === $nome && $fantasia->isDisponivel()) {
                $valorAluguel = $fantasia->calcularAluguel($dias);
                $mensagem = $fantasia->alugar();
                $this->salvarFantasias();
                return $mensagem . " Valor do aluguel: R$ " . number_format($valorAluguel, 2, ',', '.');
            }
        }
        return "Fantasia não disponível.";
    }

    /**
     * Devolve uma fantasia alugada
     */
    public function devolverFantasia(string $nome): string {
        foreach ($this->fantasias as $fantasia) {
            if ($fantasia->getNome() === $nome && !$fantasia->isDisponivel()) {
                $mensagem = $fantasia->devolver();
                $this->salvarFantasias();
                return $mensagem;
            }
        }
        return "Fantasia não encontrada ou já está disponível.";
    }

    /**
     * Retorna a lista de todas as fantasias
     */
    public function listarFantasias(): array {
        return $this->fantasias;
    }

    /**
     * Calcula uma previsão de valor do aluguel
     */
    public function calcularPrevisaoAluguel(string $tipo, int $dias): float {
        switch ($tipo) {
            case 'Profissões':
                return (new Profissões('', ''))->calcularAluguel($dias);
            case 'Animais':
                return (new Animais('', ''))->calcularAluguel($dias);
            case 'Tematica':
                return (new Tematica('', ''))->calcularAluguel($dias);
            case 'Personagens':
            default:
                return (new Personagens('', ''))->calcularAluguel($dias);
        }
    }
}
