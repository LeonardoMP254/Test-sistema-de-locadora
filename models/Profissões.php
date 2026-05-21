<?php
namespace Models;
use Interfaces\Locavel;

/**
 * Classe que representa uma fantasia do tipo Profissões
 */
class Profissões extends Veiculo implements Locavel {
    public function calcularAluguel(int $dias): float {
        return $dias * \DIARIA_PROFISSAO;
    }

    public function getTipo(): string {
        return 'Profissões';
    }

    public function alugar(): string {
        if ($this->disponivel) {
            $this->disponivel = false;
            return "Fantasia '{$this->getNome()}' alugada com sucesso!";
        }
        return "Fantasia '{$this->getNome()}' não está disponível.";
    }

    public function devolver(): string {
        if (!$this->disponivel) {
            $this->disponivel = true;
            return "Fantasia '{$this->getNome()}' devolvida com sucesso!";
        }
        return "Fantasia '{$this->getNome()}' já está na locadora.";
    }
}