<?php

class TODOList {
    private $to_dos;

    public function __construct() {
        $this->to_dos = [];
    }

    public function add_to_do($tache) {
        if (trim($tache) !== '') {
            $this->to_dos[] = $tache;
        }
    }

    public function remove_to_do($indice) {
        if (isset($this->to_dos[$indice])) {
            unset($this->to_dos[$indice]);
            $this->to_dos = array_values($this->to_dos);
        }
    }

    public function is_empty() {
        return empty($this->to_dos);
    }

    public function get_html() {
        if ($this->is_empty()) {
            return '<p>Aucune tâche à faire !</p>';
        }
        $html = '<ul>';
        foreach ($this->to_dos as $tache) {
            $html .= '<li>' . $tache . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
