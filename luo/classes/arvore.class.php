<?php
	if (!class_exists('Nodo'))
	{ include(__DIR__.'/nodo.class.php'); }
	
	if (!class_exists('Arvore')) :
	
	class Arvore {
		// Variavel - variável objetivo desta árvore
		public $objetivo;

		// bool - resolvido
		public $resolvido = false;

		// Nodo - nodo raíz da árvore
		public $raiz;

		// float - certeza da informação da variável objetivo
		public $certeza = 1;

		// void - construtor
		function __construct($objetivo){
			$this->objetivo = $objetivo;
		}
	}
	
	endif;
?>