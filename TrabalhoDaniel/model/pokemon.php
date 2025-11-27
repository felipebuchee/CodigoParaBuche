<?php
class Pokemon
{
    private int $id;
    private ?string $nome;
    private ?float $peso;
    private ?float $altura;
    private ?string $imagem;
    private ?string $cor;
    private array $tipos;  // Array de objetos Tipos
    private ?Regioes $regiao;

    public function __construct()
    {
        $this->tipos = array();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(?float $peso): self
    {
        $this->peso = $peso;

        return $this;
    }

    public function getAltura(): ?float
    {
        return $this->altura;
    }

    public function setAltura(?float $altura): self
    {
        $this->altura = $altura;

        return $this;
    }

    public function getImagem(): ?string
    {
        return $this->imagem;
    }

    public function setImagem(?string $imagem): self
    {
        $this->imagem = $imagem;

        return $this;
    }
    
    public function getCor(): ?string
    {
        return $this->cor;
    }

    public function setCor(?string $cor): self
    {
        $this->cor = $cor;

        return $this;
    }

    public function getTipos(): array
    {
        return $this->tipos;
    }

    public function setTipos(array $tipos): self
    {
        $this->tipos = $tipos;
        return $this;
    }

    public function addTipo(Tipos $tipo): self
    {
        // Evita adicionar tipos duplicados
        foreach($this->tipos as $tipoExistente) {
            if($tipoExistente->getId() == $tipo->getId()) {
                return $this;
            }
        }
        $this->tipos[] = $tipo;
        return $this;
    }

    public function removeTipo(Tipos $tipo): self
    {
        foreach($this->tipos as $key => $tipoExistente) {
            if($tipoExistente->getId() == $tipo->getId()) {
                unset($this->tipos[$key]);
                $this->tipos = array_values($this->tipos); // Reindexar array
                break;
            }
        }
        return $this;
    }

    public function getTipo(): ?Tipos
    {
        // Método de compatibilidade - retorna o primeiro tipo
        return count($this->tipos) > 0 ? $this->tipos[0] : null;
    }

    public function setTipo(?Tipos $tipo): self
    {
        // Método de compatibilidade - define como único tipo
        $this->tipos = array();
        if($tipo !== null) {
            $this->tipos[] = $tipo;
        }
        return $this;
    }

    public function getRegiao(): ?Regioes
    {
        return $this->regiao;
    }

    public function setRegiao(?Regioes $regiao): self
    {
        $this->regiao = $regiao;

        return $this;
    }
}
