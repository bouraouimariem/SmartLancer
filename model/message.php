<?php
class Messages {
    private ?int $id_message;
    private int $id_room;
    private int $id_user;
    private string $message;
    private ?DateTime $date_mes;

    public function __construct(?int $id_message, int $id_room, int $id_user, string $message, ?DateTime $date_mes) {
        $this->id_message = $id_message;
        $this->id_room = $id_room;
        $this->id_user = $id_user;
        $this->message = $message;
        $this->date_mes = $date_mes;
    }

    // --- GETTERS ---
    public function getIdMessage(): ?int { return $this->id_message; }
    public function getRoomId(): int { return $this->id_room; }
    public function getUserId(): int { return $this->id_user; }
    public function getMessage(): string { return $this->message; }
    public function getDateMes(): ?DateTime { return $this->date_mes; }

    // --- SETTERS ---
    public function setIdMessage(?int $id_message): void { $this->id_message = $id_message; }
    public function setRoomId(int $id_room): void { $this->id_room = $id_room; }
    public function setUserId(int $id_user): void { $this->id_user = $id_user; }
    public function setMessage(string $message): void { $this->message = $message; }
    public function setDateMes(?DateTime $date_mes): void { $this->date_mes = $date_mes; }
}
?>
