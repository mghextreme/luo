<?php
	if (!class_exists('Condicao'))
	{ include(__DIR__.'/condicao.class.php'); }
	
	class Nodo {
		// int - ID da pergunta a que o nodo se refere
		public $id;
		
		// Nodo[] - lista de Nodos filhos
		public $filhos = array();
		
		// Condicao[] - lista de Condicao para que as consequências sejam verdadeiras
		public $codicoes = array();
		
		// Consequencia[] - lista de Condicao para que as consequências sejam verdadeiras
		public $consequencias = array();
		
		// void - construtor
		public __construct(){
			
		}
		
		// void - gerar filhos
		public expandir(){
			
		}
	}
?>