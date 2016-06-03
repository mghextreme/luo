<?php
	class Variavel {
		// int - ID
		public $id;
		
		// string - nome
		public $nome;
		
		// string - tipo de valor
		public $tipo;
		
		// boolean - true se ela é perguntada, false se ela não pode ser perguntada
		public $questionavel;
		
		// string - o texto da pergunta
		public $pergunta;
		
		// string - uma descrição, se necessário, para a variavel
		public $descricao;
		
		// void - construtor
		public __construct($id){
			
		}
	}
?>