<?php
    class Gebruiker {
        private $idGebruiker;
        private $naam;
        private $voornaam;
        private $email;
        private $wachtwoord;
        private $niveau;

        function __construct($idGebruiker, $naam, $voornaam, $email, $wachtwoord, $niveau) {
            $this->setIdGebruiker($idGebruiker);
            $this->setNaam($naam);
            $this->setVoornaam($voornaam);
            $this->setEmail($email);
            $this->setWachtwoord($wachtwoord);
            $this->setNiveau($niveau);
        }

        private function setIdGebruiker($idGebruiker) {
            $this->idGebruiker = $idGebruiker;
        }

        private function setNaam($naam) {
            $this->naam = $naam;
        }

        private function setVoornaam($voornaam) {
            $this->voornaam = $voornaam;
        }

        private function setEmail($email) {
            $this->email = $email;
        }

        private function setWachtwoord($wachtwoord) {
            $this->wachtwoord = $wachtwoord;
        }

        private function setNiveau($niveau) {
            $this->niveau = $niveau;
        }

        public function getIdGebruiker() {
            return $this->idGebruiker;
        }

        public function getNaam() {
            return $this->naam;
        }

        public function getVoornaam() {
            return $this->voornaam;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getWachtwoord() {
            return $this->wachtwoord;
        }

        public function getNiveau() {
            return $this->niveau;
        }
    }
?>
