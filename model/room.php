<?php 
class Room{
    private ?int $id_room;
    private int $id_pub;
    private int $id_propo;
    private int $id_user1;
    private int $id_user2;
    private ?DateTime $date_room;

    public function __construct(?int $id_room,int $id_pub,int $id_propo,int $id_user1,int $id_user2, ?DateTime $date_room){
        $this->id_room =$id_room;
        $this->id_pub= $id_pub;
        $this->id_propo=$id_propo;
        $this->id_user1=$id_user1;
        $this->id_user2=$id_user2;
        $this->date_room=$date_room ?? new DateTime();
    }

    public function getIdRoom(): ?int {
        return $this->id_room;
    }

    public function getIdPub(): int {
        return $this->id_pub;
    }

    public function getIdPropo(): int {
        return $this->id_propo;
    }

    public function getIdUser1(): int {
        return $this->id_user1;
    }

    public function getIdUser2(): int {
        return $this->id_user2;
    }

    public function getDateRoom(): ?DateTime {
        return $this->date_room;
    }

    public function setIdRoom(?int $id_room): void {
        $this->id_room = $id_room;
    }

    public function setIdPub(int $id_pub): void {
        $this->id_pub = $id_pub;
    }

    public function setIdPropo(int $id_propo): void {
        $this->id_propo = $id_propo;
    }

    public function setIdUser1(int $id_user1): void {
        $this->id_user1 = $id_user1;
    }

    public function setIdUser2(int $id_user2): void {
        $this->id_user2 = $id_user2;
    }

    public function setDateRoom(?DateTime $date_room): void {
        $this->date_room = $date_room ?? new DateTime();
    }
}

?>