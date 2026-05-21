<?php
namespace Models;

/**
 * Classe abstrata base para todos os tipos de veículos
 */
abstract class Veiculo {
    protected string $nome;
    protected string $tamanho;
    protected bool $disponivel;

    public function __construct(string $nome, string $tamanho) {
        $this->nome = $nome;
        $this->tamanho = $tamanho;
        $this->disponivel = true;
    }

    /**
     * Calcula o valor do aluguel baseado na quantidade de dias
     */
    abstract public function calcularAluguel(int $dias): float;

    abstract public function getTipo(): string;

    public function isDisponivel(): bool {
        return $this->disponivel;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getTamanho(): string {
        return $this->tamanho;
    }

    public function setDisponivel(bool $disponivel): void {
        $this->disponivel = $disponivel;
    }
}