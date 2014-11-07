<?php

class log {
    private $file; 
    
	function __construct($name) {
        $this->file = "log_".str_replace('.', '_', $name).".txt" ; 
    }
	
    function debug($log) {
        $log = date('Y-m-d H:i:s')." ".$log."\n"; 
        $maj = fopen($this->file,"a+"); // On ouvre le fichier en lecture/écriture
        fseek($maj, 0, SEEK_END);
        fputs($maj, $log);            // On écrit dans le fichier
        fclose($maj);    
    }
    
    function clean() {
        $maj = fopen($this->file,"a+"); // On ouvre le fichier en lecture/écriture
        ftruncate($maj,0);            // on efface le contenu d'un fichier
        fclose($maj);  
    }
 
}
?>