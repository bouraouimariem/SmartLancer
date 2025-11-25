<?php
class Publications {
    private ?int $id_pub;
    private int $id_user;
    private string $nom_pub;
    private ?string $categorie;
    private ?string $description;
    private ?float $budget;
    private ?string $delai_requise; 
    private ?DateTime $date_pub; 
    private ?string $status;

    // Constructor
    public function __construct(
        ?int $id_pub, 
        int $id_user, 
        string $nom_pub, 
        ?string $categorie, 
        ?string $description, 
        ?float $budget, 
        ?string $delai_requise, 
        ?DateTime $date_pub = null, 
        string $status = "en cours"
    ) {
        $this->id_pub = $id_pub;
        $this->id_user = $id_user;
        $this->nom_pub = $nom_pub;
        $this->categorie = $categorie;
        $this->description = $description;
        $this->budget = $budget;
        $this->delai_requise = $delai_requise;
        $this->date_pub = $date_pub ?? new DateTime(); 
        $this->status = $status;
    }

    public function getIdPub(): ?int {
        return $this->id_pub;
    }

    public function setIdPub(int $id_pub): void {
        $this->id_pub = $id_pub;
    }

    public function getIdUser(): int {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): void {
        $this->id_user = $id_user;
    }

    public function getNomPub(): string {
        return $this->nom_pub;
    }

    public function setNomPub(string $nom_pub): void {
        $this->nom_pub = $nom_pub;
    }

    public function getCategorie(): ?string {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): void {
        $this->categorie = $categorie;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getBudget(): ?float {
        return $this->budget;
    }

    public function setBudget(?float $budget): void {
        $this->budget = $budget;
    }

    public function getDelaiRequise(): ?string {
        return $this->delai_requise;
    }

    public function setDelaiRequise(?string $delai_requise): void {
        $this->delai_requise = $delai_requise;
    }

    public function getDatePub(): ?DateTime {
        return $this->date_pub;
    }

    public function setDatePub(DateTime $date_pub): void {
        $this->date_pub = $date_pub;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }
}
?>
