<?php
class Propositions {
    private ?int $id_propo;
    private int $id_user;
    private int $id_pub;
    private ?string $commentaire;
    private ?float $montant_prop;
    private ?string $delai_estime; 
    private ?DateTime $date_propo;
    private string $status;

    public function __construct(?int $id_propo, int $id_user,int $id_pub,?string $commentaire, ?float $montant_prop,?string $delai_estime,?DateTime $date_propo, string $status = "en attente") {
        $this->id_propo = $id_propo;
        $this->id_user = $id_user;
        $this->id_pub = $id_pub;
        $this->commentaire = $commentaire;
        $this->montant_prop = $montant_prop;
        $this->delai_estime = $delai_estime;
        $this->date_propo = $date_propo ?? new DateTime();
        $this->status = $status;
    }
    public function getIdPropo(): ?int {
        return $this->id_propo;
    }

    public function setIdPropo(int $id_propo): void {
        $this->id_propo = $id_propo;
    }

    public function getIdUser(): int {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): void {
        $this->id_user = $id_user;
    }

    public function getIdPub(): int {
        return $this->id_pub;
    }

    public function setIdPub(int $id_pub): void {
        $this->id_pub = $id_pub;
    }

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): void {
        $this->commentaire = $commentaire;
    }

    public function getMontantProp(): ?float {
        return $this->montant_prop;
    }

    public function setMontantProp(?float $montant_prop): void {
        $this->montant_prop = $montant_prop;
    }

    public function getDelaiEstime(): ?string {
        return $this->delai_estime;
    }

    public function setDelaiEstime(?string $delai_estime): void {
        $this->delai_estime = $delai_estime;
    }

    public function getDatePropo(): ?DateTime {
        return $this->date_propo;
    }

    public function setDatePropo(DateTime $date_propo): void {
        $this->date_propo = $date_propo;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }
}
?>
